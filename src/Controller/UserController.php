<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 10:11 PM
 */

declare(strict_types=1);

namespace Dot\User\Controller;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Controller\AbstractActionController;
use Dot\Helpers\Psr7\HttpMessagesAwareInterface;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Form\ChangePasswordForm;
use Dot\User\Form\ForgotPasswordForm;
use Dot\User\Form\LoginForm;
use Dot\User\Form\RegisterForm;
use Dot\User\Form\ResetPasswordForm;
use Dot\User\Form\UserForm;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\ResultInterface;
use Dot\User\Result\UserOperationResult;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Form\Form;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;

/**
 * Class UserController
 * @package Dot\User\Controller
 *
 * @method UrlHelperPlugin|UriInterface url(string $route = null, array $params = [])
 * @method FlashMessengerPlugin messenger()
 * @method FormsPlugin|Form forms(string $name = null)
 * @method TemplatePlugin|string template(string $template = null, array $params = [])
 * @method AuthenticationPlugin authentication()
 * @method AuthorizationPlugin isGranted(string $permission, array $roles = [], mixed $context = null)
 */
class UserController extends AbstractActionController
{
    const LOGIN_ROUTE_NAME = 'login';

    /** @var  UserOptions */
    protected $options;

    /** @var  LoginAction */
    protected $loginAction;

    /** @var  UserServiceInterface */
    protected $userService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param LoginAction $loginAction
     * @param UserOptions $options
     */
    public function __construct(
        UserServiceInterface $userService,
        LoginAction $loginAction,
        UserOptions $options
    ) {
        $this->userService = $userService;
        $this->options = $options;
        $this->loginAction = $loginAction;
    }

    /**
     * @return mixed
     */
    public function dispatch(): ResponseInterface
    {
        //set request/response object on user service for each request
        if ($this->userService instanceof HttpMessagesAwareInterface) {
            $this->userService->setServerRequest($this->getRequest());
            $this->userService->setResponse($this->getResponse());
        }

        return parent::dispatch();
    }

    /**
     * @return ResponseInterface
     */
    public function confirmAccountAction(): ResponseInterface
    {
        if (!$this->options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
            $this->messenger()->addError($this->options->getMessagesOptions()->getMessage(
                MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_DISABLED
            ));

            return new RedirectResponse($this->url(self::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = $params['email'] ?? '';
        $token = $params['token'] ?? '';

        /** @var ResultInterface $result */
        $result = $this->userService->confirmAccount($email, $token);
        if (!$result->isValid()) {
            $this->messenger()->addError($result->getMessages());
        } else {
            $this->messenger()->addSuccess($result->getMessages());
        }

        return new RedirectResponse($this->url(self::LOGIN_ROUTE_NAME));
    }

    /**
     * @return ResponseInterface
     */
    public function changePasswordAction(): ResponseInterface
    {
        $request = $this->getRequest();

        /** @var Form $form */
        $form = $this->forms(ChangePasswordForm::class);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();

                $oldPassword = $data['password'];
                $newPassword = $data['newPassword'];

                /** @var UserOperationResult $result */
                $result = $this->userService->changePassword($oldPassword, $newPassword);
                if ($result->isValid()) {
                    $this->messenger()->addSuccess($result->getMessages());
                    return $this->redirectTo($request->getUri(), $request->getQueryParams());
                } else {
                    $this->messenger()->addError($result->getMessages());
                    $this->forms()->saveState($form);
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template(
                $this->options->getTemplateOptions()->getChangePasswordTemplate(),
                [
                    'form' => $form,
                    'showLabels' => $this->options->isShowFormInputLabels(),
                    'layoutTemplate' => $this->options->getTemplateOptions()->getChangePasswordTemplateLayout()
                ]
            )
        );
    }

    /**
     * @param UriInterface $defaultUri
     * @param array $queryParams
     * @return ResponseInterface
     */
    protected function redirectTo(UriInterface $defaultUri, array $queryParams = []): ResponseInterface
    {
        if (isset($queryParams['redirect'])) {
            $uri = new Uri(urldecode($queryParams['redirect']));
        } else {
            $uri = $defaultUri;
        }

        return new RedirectResponse($uri);
    }

    /**
     * @return ResponseInterface
     */
    public function registerAction(): ResponseInterface
    {
        $request = $this->getRequest();

        if (!$this->options->getRegisterOptions()->isEnableRegistration()) {
            $this->messenger()->addError($this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REGISTER_DISABLED));

            return new RedirectResponse($this->url(self::LOGIN_ROUTE_NAME));
        }

        /** @var RegisterForm $form */
        $form = $this->forms(RegisterForm::class);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->bind($this->userService->getMapper()->getPrototype());
            $form->setData($data);
            if ($form->isValid()) {
                /** @var UserEntityInterface $entity */
                $entity = $form->getData();

                /** @var UserOperationResult $result */
                $result = $this->userService->register($entity);
                if (!$result->isValid()) {
                    $this->messenger()->addError($result->getMessages());
                    $this->forms()->saveState($form);
                    return new RedirectResponse($request->getUri(), 303);
                } else {
                    $user = $result->getUser();
                    if ($this->options->getRegisterOptions()->isLoginAfterRegistration()) {
                        return $this->autoLoginUser($user, $data['password']);
                    } else {
                        $this->messenger()->addSuccess($result->getMessages());
                        return $this->redirectTo(
                            $this->url(self::LOGIN_ROUTE_NAME),
                            $request->getQueryParams()
                        );
                    }
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template(
                $this->options->getTemplateOptions()->getRegisterTemplate(),
                [
                    'form' => $form,
                    'enableRegistration' => $this->options->getRegisterOptions()->isEnableRegistration(),
                    'showLabels' => $this->options->isShowFormInputLabels(),
                    'layoutTemplate' => $this->options->getTemplateOptions()->getRegisterTemplateLayout()
                ]
            )
        );
    }

    /**
     * @return ResponseInterface
     * @throws \Exception
     */
    public function accountAction(): ResponseInterface
    {
        $request = $this->getRequest();

        /** @var UserForm $form */
        $form = $this->forms(UserForm::class);

        /** @var UserEntityInterface $identity */
        $identity = $this->authentication()->getIdentity();
        $user = $this->userService->find([$this->userService->getMapper()->getIdentifierName() => $identity->getId()]);
        $user->setPassword(null);

        //this should never happen, that's why we treat it as exception
        if (!$user instanceof UserEntityInterface) {
            throw new RuntimeException('Could not load user entity for identity ID');
        }

        $form->bind($user);
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            //in case username is changed we need to check its uniqueness
            //but only in case username was actually changed from the previous one
            if (isset($data['user']['username']) && $data['user']['username'] === $user->getUsername()) {
                //consider we don't want to change username, remove the uniqueness check
                $form->removeUsernameValidation();
            }
            if (isset($data['user']['email']) && $data['user']['email'] === $user->getEmail()) {
                //consider we don't want to change email, remove the uniqueness check
                $form->removeEmailValidation();
            }

            $form->applyValidationGroup();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var UserEntityInterface $user */
                $user = $form->getData();

                /** @var UserOperationResult $result */
                $result = $this->userService->updateAccount($user);

                if ($result->isValid()) {
                    $this->messenger()->addSuccess($result->getMessages());
                    return new RedirectResponse($request->getUri());
                } else {
                    $this->messenger()->addError($result->getMessages());
                    $this->forms()->saveState($form);
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template(
                $this->options->getTemplateOptions()->getAccountTemplate(),
                [
                    'form' => $form,
                    'showLabels' => $this->options->isShowFormInputLabels(),
                    'layoutTemplate' => $this->options->getTemplateOptions()->getAccountTemplateLayout()
                ]
            )
        );
    }

    /**
     * Show the reset password form, validate data
     *
     * @return ResponseInterface
     */
    public function resetPasswordAction(): ResponseInterface
    {
        if (!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->messenger()->addError(
                $this->options->getMessagesOptions()->getMessage(
                    MessagesOptions::MESSAGE_RESET_PASSWORD_DISABLED
                )
            );

            return new RedirectResponse($this->url(self::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        /** @var Form $form */
        $form = $this->forms(ResetPasswordForm::class);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            if ($form->isValid()) {
                $newPassword = $data['newPassword'];

                /** @var UserOperationResult $result */
                $result = $this->userService->resetPassword($email, $token, $newPassword);

                if (!$result->isValid()) {
                    $this->messenger()->addError($result->getMessages());
                    $this->forms()->saveState($form);
                    return new RedirectResponse($request->getUri(), 303);
                } else {
                    $this->messenger()->addSuccess($result->getMessages());
                    return $this->redirectTo(
                        $this->url(self::LOGIN_ROUTE_NAME),
                        $request->getQueryParams()
                    );
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template(
                $this->options->getTemplateOptions()->getResetPasswordTemplate(),
                [
                    'form' => $form,
                    'showLabels' => $this->options->isShowFormInputLabels(),
                    'layoutTemplate' => $this->options->getTemplateOptions()->getResetPasswordTemplateLayout()
                ]
            )
        );
    }

    /**
     * @return ResponseInterface
     */
    public function forgotPasswordAction(): ResponseInterface
    {
        if (!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->messenger()->addError(
                $this->options->getMessagesOptions()->getMessage(
                    MessagesOptions::MESSAGE_RESET_PASSWORD_DISABLED
                )
            );

            return new RedirectResponse($this->url(self::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();

        /** @var Form $form */
        $form = $this->forms(ForgotPasswordForm::class);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData();
                $email = $data['email'];

                /** @var UserOperationResult $result */
                $result = $this->userService->generateResetToken($email);
                if ($result->isValid()) {
                    $this->messenger()->addInfo($result->getMessages());
                    return $this->redirectTo(
                        $this->url(self::LOGIN_ROUTE_NAME),
                        $request->getQueryParams()
                    );
                } else {
                    $this->messenger()->addError($result->getMessages());
                    $this->forms()->saveState($form);
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form));
                $this->forms()->saveState($form);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template(
                $this->options->getTemplateOptions()->getForgotPasswordTemplate(),
                [
                    'form' => $form,
                    'showLabels' => false,
                    'layoutTemplate' => $this->options->getTemplateOptions()->getForgotPasswordTemplateLayout()
                ]
            )
        );
    }

    /**
     * Force an auth event using the LoginAction to automatically login the user after registration
     *
     * @param UserEntityInterface $user
     * @param string $password
     * @return mixed
     */
    protected function autoLoginUser(UserEntityInterface $user, string $password)
    {
        /** @var ServerRequestInterface $request */
        $request = $this->getRequest();

        /** @var Form $form */
        $form = $this->forms(LoginForm::class);
        $csrf = $form->get('login_csrf');

        $loginData = [
            'identity' => $user->getEmail(),
            'password' => $password,
            'remember' => 'no',
        ];

        if ($csrf) {
            $loginData[$csrf->getName()] = $csrf->getValue();
        }

        $form->setData($loginData);
        $form->isValid();

        $request = $request->withParsedBody($form->getData())
            ->withUri(new Uri($this->url(self::LOGIN_ROUTE_NAME)));

        return $this->loginAction->triggerAuthenticateEvent($request, $request->getParsedBody());
    }
}
