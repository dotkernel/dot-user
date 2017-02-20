<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/15/2017
 * Time: 4:13 PM
 */

declare(strict_types = 1);

namespace Dot\User\Controller;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Controller\AbstractActionController;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Dot\User\Entity\UserEntity;
use Dot\User\Exception\InvalidArgumentException;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\Result;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Form\Form;
use Zend\Form\FormInterface;

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
    protected $userOptions;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  LoginAction */
    protected $loginAction;

    /**
     * UserController constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['user_options']) && $options['user_options'] instanceof UserOptions) {
            $this->userOptions = $options['user_options'];
        }

        if (isset($options['user_service']) && $options['user_service'] instanceof UserServiceInterface) {
            $this->userService = $options['user_service'];
        }

        if (isset($options['login_action']) && $options['login_action'] instanceof LoginAction) {
            $this->loginAction = $options['login_action'];
        }

        if (!$this->userService instanceof UserServiceInterface) {
            throw new InvalidArgumentException('User service is required and was not set');
        }

        if (!$this->userOptions instanceof UserOptions) {
            throw new InvalidArgumentException('UserOptions is required and was not set');
        }

        if ($this->userOptions->getRegisterOptions()->isLoginAfterRegistration()
            && !$this->loginAction instanceof LoginAction
        ) {
            throw new InvalidArgumentException('LoginAction is required for auto-login feature and was not set');
        }
    }

    /**
     * @return ResponseInterface
     */
    public function confirmAccountAction(): ResponseInterface
    {
        if ($this->userOptions->isEnableAccountConfirmation()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_DISABLED));
            return new RedirectResponse($this->url(static::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $result = $this->userService->confirmAccount($params);
        if ($result->isValid()) {
            $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_SUCCESS));
        } else {
            $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_ERROR));
            $this->messenger()->addError($message);
        }

        return new RedirectResponse($this->url(static::LOGIN_ROUTE_NAME));
    }

    /**
     * @return ResponseInterface
     */
    public function changePasswordAction(): ResponseInterface
    {
        if (!$this->authentication()->hasIdentity()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::UNAUTHORIZED));
            return new RedirectResponse($this->url(static::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $form = $this->forms('ChangePassword');
        $identity = $this->authentication()->getIdentity();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $user = $this->userService->find($identity->getId());
            if ($user) {
                $form->setBindOnValidate(FormInterface::BIND_MANUAL);
                $form->setData($data);
                if ($form->isValid()) {
                    $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

                    $result = $this->userService->changePassword($user, $data);
                    if ($result->isValid()) {
                        $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::CHANGE_PASSWORD_SUCCESS));

                        return $this->redirectTo($request->getUri(), $request->getQueryParams());
                    } else {
                        $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::CHANGE_PASSWORD_ERROR));
                        $this->messenger()->addError($message);
                        $this->forms()->saveState($form);

                        return new RedirectResponse($request->getUri(), 303);
                    }
                } else {
                    $this->messenger()->addError($this->forms()->getMessages($form));
                    $this->forms()->saveState($form);

                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::USER_NOT_FOUND));

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template(
                $this->userOptions->getTemplateOptions()->getChangePasswordTemplate(),
                [
                    'form' => $form,
                    'showLabels' => $this->userOptions->isShowFormLabels(),
                    'layoutTemplate' => $this->userOptions->getTemplateOptions()->getChangePasswordTemplateLayout()
                ]
            )
        );
    }

    /**
     * @return ResponseInterface
     */
    public function registerAction(): ResponseInterface
    {
        $request = $this->getRequest();
        if ($this->userOptions->getRegisterOptions()->isEnableRegistration()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::REGISTER_DISABLED));
            return new RedirectResponse($this->url(static::LOGIN_ROUTE_NAME));
        }

        $form = $this->forms('Register');

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->bind($this->userService->newUser());
            $form->setData($data);
            if ($form->isValid()) {
                /** @var UserEntity $user */
                $user = $form->getData();

                $result = $this->userService->register($user);
                if ($result->isValid()) {
                    if ($this->userOptions->getRegisterOptions()->isLoginAfterRegistration()) {
                        return $this->autoLogin($user, $data['password']);
                    } else {
                        $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::REGISTER_SUCCESS));

                        return $this->redirectTo(
                            $this->url(static::LOGIN_ROUTE_NAME),
                            $request->getQueryParams()
                        );
                    }
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::USER_REGISTER_ERROR));
                    $this->messenger()->addError($message);
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
                $this->userOptions->getTemplateOptions()->getRegisterTemplate(),
                [
                    'form' => $form,
                    'enableRegistration' => $this->userOptions->getRegisterOptions()->isEnableRegistration(),
                    'showLabels' => $this->userOptions->isShowFormLabels(),
                    'layoutTemplate' => $this->userOptions->getTemplateOptions()->getRegisterTemplateLayout()
                ]
            )
        );
    }

    /**
     * @return ResponseInterface
     */
    public function accountAction(): ResponseInterface
    {
        if (!$this->authentication()->hasIdentity()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::UNAUTHORIZED));
            return new RedirectResponse($this->url(static::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $form = $this->forms('Account');

        $user = $this->userService->find($this->authentication()->getIdentity()->getId());
        $form->bind($user);
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            // dynamically change form validation groups, according to form data
            $this->onBeforeAccountFormValidation($user, $form, $data);

            $form->setData($data);
            if ($form->isValid()) {
                $user = $form->getData();

                $result = $this->userService->updateAccount($user);
                if ($result->isValid()) {
                    $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::USER_UPDATE_SUCCESS));

                    return new RedirectResponse($request->getUri());
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::USER_UPDATE_ERROR));
                    $this->messenger()->addError($message);
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
            $this->userOptions->getTemplateOptions()->getAccountTemplate(),
            [
                'form' => $form,
                'showLabels' => $this->userOptions->isShowFormLabels(),
                'layoutTemplate' => $this->userOptions->getTemplateOptions()->getAccountTemplateLayout()
            ]
        );
    }

    /**
     * @return ResponseInterface
     */
    public function resetPasswordAction(): ResponseInterface
    {
        if (!$this->userOptions->getPasswordRecoveryOptions()->isEnableRecovery()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::RESET_PASSWORD_DISABLED));
            return new RedirectResponse($this->url(static::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $form = $this->forms('ResetPassword');
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setBindOnValidate(FormInterface::BIND_MANUAL);
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY);
                $data = array_merge($params, $data);

                $result = $this->userService->resetPassword($data);
                if ($result->isValid()) {
                    $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::RESET_PASSWORD_SUCCESS));

                    return $this->redirectTo($this->url(static::LOGIN_ROUTE_NAME), $request->getQueryParams());
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::RESET_PASSWORD_ERROR));
                    $this->messenger()->addError($message);
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
                $this->userOptions->getTemplateOptions()->getResetPasswordTemplate(),
                [
                    'form' => $form,
                    'showLabels' => $this->userOptions->isShowFormLabels(),
                    'layoutTemplate' => $this->userOptions->getTemplateOptions()->getResetPasswordTemplateLayout()
                ]
            )
        );
    }

    /**
     * @return ResponseInterface
     */
    public function forgotPasswordAction(): ResponseInterface
    {
        if (!$this->userOptions->getPasswordRecoveryOptions()->isEnableRecovery()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::RESET_PASSWORD_DISABLED));
            return new RedirectResponse($this->url(static::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $form = $this->forms('ForgotPassword');

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $form->setData($data);

            if ($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

                $result = $this->userService->resetPasswordRequest($data);
                if ($result->isValid()) {
                    $this->messenger()->addInfo($this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::FORGOT_PASSWORD_SUCCESS));

                    return $this->redirectTo($this->url(static::LOGIN_ROUTE_NAME), $request->getQueryParams());
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::RESET_TOKEN_SAVE_ERROR));
                    $this->messenger()->addError($message);
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
                $this->userOptions->getTemplateOptions()->getForgotPasswordTemplate(),
                [
                    'form' => $form,
                    'showLabels' => false,
                    'layoutTemplate' => $this->userOptions->getTemplateOptions()->getForgotPasswordTemplateLayout()
                ]
            )
        );
    }

    /**
     * @param UriInterface $defaultUri
     * @param array $queryParams
     * @return ResponseInterface
     */
    public function redirectTo(UriInterface $defaultUri, array $queryParams = []): ResponseInterface
    {
        if (isset($queryParams['redirect'])) {
            $uri = new Uri(urldecode($queryParams['redirect']));
        } else {
            $uri = $defaultUri;
        }

        return new RedirectResponse($uri);
    }

    /**
     * You can override this method to change the form validation on the go, for account update
     * @param UserEntity $user
     * @param Form $form
     * @param array $data
     */
    protected function onBeforeAccountFormValidation(UserEntity $user, Form $form, array $data)
    {
        if ($data['user']['username'] === $user->getUsername()) {
            $validationGroup = $form->getValidationGroup();
            unset($validationGroup['user']['username']);
            $form->setValidationGroup($validationGroup);
        }
    }

    /**
     * @param UserEntity $user
     * @param string $password
     * @return ResponseInterface
     */
    protected function autoLogin(UserEntity $user, string $password): ResponseInterface
    {
        $request = $this->getRequest();
        $form = $this->forms('Login');
        $csrf = $form->get('login_csrf');

        $loginData = [
            'identity' => $user->getEmail(),
            'password' => $password,
            'remember' => 'no'
        ];

        if ($csrf) {
            $loginData[$csrf->getName()] = $csrf->getValue();
        }

        $form->setData($loginData);
        $form->isValid();

        /** @var ServerRequestInterface $request */
        $request = $request->withParsedBody($form->getData())
            ->withUri(new Uri($this->url(static::LOGIN_ROUTE_NAME)));

        return $this->loginAction->triggerAuthenticateEvent($request, $request->getParsedBody());
    }

    /**
     * @param Result $result
     * @param string $defaultErrorMessage
     * @return string
     */
    protected function getResultErrorMessage(Result $result, string $defaultErrorMessage)
    {
        if ($result->isValid()) {
            return 'Success!';
        }

        $error = $result->getError();
        if (is_string($error)) {
            return $error;
        }

        if (is_array($error)) {
            $errors = [];
            foreach ($error as $e) {
                if (is_string($e)) {
                    $errors[] = $e;
                }
            }
            return $errors;
        }

        if ($error instanceof \Exception && $this->isDebug()) {
            return $error->getMessage();
        }

        return $defaultErrorMessage;
    }
}
