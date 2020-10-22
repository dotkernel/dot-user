<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Controller;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Controller\AbstractActionController;
use Dot\Controller\Plugin\Authentication\AuthenticationPlugin;
use Dot\Controller\Plugin\Authorization\AuthorizationPlugin;
use Dot\Controller\Plugin\FlashMessenger\FlashMessengerPlugin;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\Controller\Plugin\TemplatePlugin;
use Dot\Controller\Plugin\UrlHelperPlugin;
use Dot\Helpers\Route\RouteHelper;
use Dot\User\Entity\UserEntity;
use Dot\User\Event\DispatchUserControllerEventsTrait;
use Dot\User\Event\UserControllerEvent;
use Dot\User\Event\UserControllerEventListenerInterface;
use Dot\User\Event\UserControllerEventListenerTrait;
use Dot\User\Exception\InvalidArgumentException;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\Result;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Uri;
use Laminas\Form\Form;
use Laminas\Form\FormInterface;

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
class UserController extends AbstractActionController implements UserControllerEventListenerInterface
{
    use DispatchUserControllerEventsTrait;
    use UserControllerEventListenerTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /** @var  WebAuthenticationOptions */
    protected $webAuthenticationOptions;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  LoginAction */
    protected $loginAction;

    /** @var  RouteHelper */
    protected $routeHelper;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param UserOptions $userOptions
     * @param WebAuthenticationOptions $webAuthenticationOptions
     * @param RouteHelper $routeHelper
     * @param LoginAction|null $loginAction
     */
    public function __construct(
        UserServiceInterface $userService,
        UserOptions $userOptions,
        WebAuthenticationOptions $webAuthenticationOptions,
        RouteHelper $routeHelper,
        LoginAction $loginAction = null
    ) {
        $this->webAuthenticationOptions = $webAuthenticationOptions;
        $this->userService = $userService;
        $this->userOptions = $userOptions;
        $this->loginAction = $loginAction;
        $this->routeHelper = $routeHelper;

        if ($this->userOptions->getRegisterOptions()->isLoginAfterRegistration()
            && !$this->loginAction instanceof LoginAction
        ) {
            throw new InvalidArgumentException('LoginAction is required for auto-login feature and was not set');
        }
    }

    /**
     * @return ResponseInterface
     */
    public function optOutAction(): ResponseInterface
    {
        if ($this->authentication()->hasIdentity()) {
            $this->messenger()->addWarning($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::SIGN_OUT_FIRST));
            return new RedirectResponse($this->routeHelper->generateUri($this->userOptions->getRouteDefault()));
        }

        if (!$this->userOptions->isEnableAccountConfirmation()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_DISABLED));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $result = $this->userService->optOut($params);
        if ($result->isValid()) {
            $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::OPT_OUT_SUCCESS));
        } else {
            $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::OPT_OUT_ERROR));
            $this->messenger()->addError($message);
        }

        return new RedirectResponse($this->routeHelper
            ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
    }

    /**
     * @return ResponseInterface
     */
    public function confirmAccountAction(): ResponseInterface
    {
        if ($this->authentication()->hasIdentity()) {
            $this->messenger()->addWarning($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::SIGN_OUT_FIRST));
            return new RedirectResponse($this->routeHelper->generateUri($this->userOptions->getRouteDefault()));
        }

        if (!$this->userOptions->isEnableAccountConfirmation()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_DISABLED));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
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

        return new RedirectResponse($this->routeHelper
            ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
    }

    /**
     * @return ResponseInterface
     */
    public function changePasswordAction(): ResponseInterface
    {
        if (!$this->authentication()->hasIdentity()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::UNAUTHORIZED));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
        }

        $request = $this->getRequest();
        $form = $this->forms('ChangePassword');
        $identity = $this->authentication()->getIdentity();

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();
            $user = $this->userService->find($identity->getId(), [
                'conditions' =>
                    ['status' => $this->userOptions->getLoginOptions()->getAllowedStatus()]
            ]);

            if (!$user) {
                // could happen if user is deleted during its session
                $this->authentication()->clearIdentity();
                $this->messenger()->addError($this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::ACCOUNT_INVALID));
                return new RedirectResponse($this->routeHelper
                    ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
            }

            $form->setBindOnValidate(FormInterface::BIND_MANUAL);
            $form->setData($data);
            if ($form->isValid()) {
                $data = $form->getData(FormInterface::VALUES_AS_ARRAY);

                $result = $this->userService->changePassword($user, $data);
                if ($result->isValid()) {
                    $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::CHANGE_PASSWORD_SUCCESS), 'change-password');

                    return $this->redirectTo($request->getUri(), $request->getQueryParams());
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::CHANGE_PASSWORD_ERROR));
                    $this->messenger()->addError($message, 'change-password');
                    $this->forms()->saveState($form);

                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form), 'change-password');
                $this->forms()->saveState($form);

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        $r = $this->dispatchEvent(UserControllerEvent::EVENT_CONTROLLER_BEFORE_CHANGE_PASSWORD_RENDER, [
            'form' => $form,
            'request' => $this->getRequest(),
            'template' => $this->userOptions->getTemplateOptions()->getChangePasswordTemplate()
        ]);
        if ($r instanceof ResponseInterface) {
            return $r;
        }

        $params = $r->getParams();
        $template = $params['template'] ?? '';
        unset($params['template']);
        $data = $params;

        return new HtmlResponse($this->template($template, $data));
    }

    /**
     * @return ResponseInterface
     */
    public function registerAction(): ResponseInterface
    {
        if ($this->authentication()->hasIdentity()) {
            $this->messenger()->addWarning($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::SIGN_OUT_FIRST));
            return new RedirectResponse($this->routeHelper->generateUri($this->userOptions->getRouteDefault()));
        }

        if (!$this->userOptions->getRegisterOptions()->isEnableRegistration()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::REGISTER_DISABLED));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
        }

        $request = $this->getRequest();
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
                        return $this->autoLogin($user, $data['user']['password']);
                    } else {
                        $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::REGISTER_SUCCESS));
                        return $this->redirectTo(
                            $this->routeHelper->generateUri($this->webAuthenticationOptions->getLoginRoute()),
                            $request->getQueryParams()
                        );
                    }
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::USER_REGISTER_ERROR));
                    $this->messenger()->addError($message, 'register');
                    $this->forms()->saveState($form);

                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form), 'register');
                $this->forms()->saveState($form);

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        $r = $this->dispatchEvent(UserControllerEvent::EVENT_CONTROLLER_BEFORE_REGISTER_RENDER, [
            'form' => $form,
            'request' => $this->getRequest(),
            'template' => $this->userOptions->getTemplateOptions()->getRegisterTemplate()
        ]);
        if ($r instanceof ResponseInterface) {
            return $r;
        }

        $params = $r->getParams();
        $template = $params['template'] ?? '';
        unset($params['template']);
        $data = $params;

        return new HtmlResponse($this->template($template, $data));
    }

    /**
     * @return ResponseInterface
     */
    public function accountAction(): ResponseInterface
    {
        if (!$this->authentication()->hasIdentity()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::UNAUTHORIZED));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
        }

        $request = $this->getRequest();
        $form = $this->forms('Account');

        $user = $this->userService->find($this->authentication()->getIdentity()->getId(), [
            'conditions' => ['status' => $this->userOptions->getLoginOptions()->getAllowedStatus()]
        ]);
        if (!$user) {
            // could happen if user is deleted/disabled during its session
            $this->authentication()->clearIdentity();
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::ACCOUNT_INVALID));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
        }

        $form->bind($user);
        $this->forms()->restoreState($form);
        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $this->dispatchEvent(UserControllerEvent::EVENT_CONTROLLER_BEFORE_ACCOUNT_UPDATE_FORM_VALIDATION, [
                'user' => $user,
                'form' => $form,
                'data' => $data,
                'request' => $this->getRequest()
            ]);

            $form->setData($data);
            if ($form->isValid()) {
                $user = $form->getData();
                $hashPassword = array_key_exists('password', $data) ? true : false;
                $result = $this->userService->updateAccount($user, $hashPassword);
                if ($result->isValid()) {
                    $this->messenger()->addSuccess($this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::USER_UPDATE_SUCCESS), 'account');

                    return new RedirectResponse($request->getUri());
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::USER_UPDATE_ERROR));
                    $this->messenger()->addError($message, 'account');
                    $this->forms()->saveState($form);

                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form), 'account');
                $this->forms()->saveState($form);

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        $r = $this->dispatchEvent(UserControllerEvent::EVENT_CONTROLLER_BEFORE_ACCOUNT_RENDER, [
            'form' => $form,
            'request' => $this->getRequest(),
            'template' => $this->userOptions->getTemplateOptions()->getAccountTemplate()
        ]);
        if ($r instanceof ResponseInterface) {
            return $r;
        }

        $params = $r->getParams();
        $template = $params['template'] ?? '';
        unset($params['template']);
        $data = $params;

        return new HtmlResponse($this->template($template, $data));
    }

    /**
     * @return ResponseInterface
     */
    public function resetPasswordAction(): ResponseInterface
    {
        if ($this->authentication()->hasIdentity()) {
            $this->messenger()->addWarning($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::SIGN_OUT_FIRST));
            return new RedirectResponse($this->routeHelper->generateUri($this->userOptions->getRouteDefault()));
        }

        if (!$this->userOptions->getPasswordRecoveryOptions()->isEnableRecovery()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::RESET_PASSWORD_DISABLED));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
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

                    return $this->redirectTo(
                        $this->routeHelper->generateUri($this->webAuthenticationOptions->getLoginRoute()),
                        $request->getQueryParams()
                    );
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::RESET_PASSWORD_ERROR));

                    $this->messenger()->addError($message, 'reset-password');
                    $this->forms()->saveState($form);

                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form), 'reset-password');
                $this->forms()->saveState($form);

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        $r = $this->dispatchEvent(UserControllerEvent::EVENT_CONTROLLER_BEFORE_RESET_PASSWORD_RENDER, [
            'form' => $form,
            'request' => $this->getRequest(),
            'template' => $this->userOptions->getTemplateOptions()->getResetPasswordTemplate()
        ]);
        if ($r instanceof ResponseInterface) {
            return $r;
        }

        $params = $r->getParams();
        $template = $params['template'] ?? '';
        unset($params['template']);
        $data = $params;

        return new HtmlResponse($this->template($template, $data));
    }

    /**
     * @return ResponseInterface
     */
    public function forgotPasswordAction(): ResponseInterface
    {
        if ($this->authentication()->hasIdentity()) {
            $this->messenger()->addWarning($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::SIGN_OUT_FIRST));
            return new RedirectResponse($this->routeHelper->generateUri($this->userOptions->getRouteDefault()));
        }

        if (!$this->userOptions->getPasswordRecoveryOptions()->isEnableRecovery()) {
            $this->messenger()->addError($this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::RESET_PASSWORD_DISABLED));
            return new RedirectResponse($this->routeHelper
                ->generateUri($this->webAuthenticationOptions->getLoginRoute()));
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
                    $this->messenger()->addInfo(sprintf($this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::FORGOT_PASSWORD_SUCCESS), $data['email']));

                    return $this->redirectTo(
                        $this->routeHelper->generateUri($this->webAuthenticationOptions->getLoginRoute()),
                        $request->getQueryParams()
                    );
                } else {
                    $message = $this->getResultErrorMessage($result, $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::RESET_TOKEN_SAVE_ERROR));
                    $this->messenger()->addError($message, 'forgot-password');
                    $this->forms()->saveState($form);

                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->messenger()->addError($this->forms()->getMessages($form), 'forgot-password');
                $this->forms()->saveState($form);

                return new RedirectResponse($request->getUri(), 303);
            }
        }

        $r = $this->dispatchEvent(UserControllerEvent::EVENT_CONTROLLER_BEFORE_FORGOT_PASSWORD_RENDER, [
            'form' => $form,
            'request' => $this->getRequest(),
            'template' => $this->userOptions->getTemplateOptions()->getForgotPasswordTemplate()
        ]);
        if ($r instanceof ResponseInterface) {
            return $r;
        }

        $params = $r->getParams();
        $template = $params['template'] ?? '';
        unset($params['template']);
        $data = $params;

        return new HtmlResponse($this->template($template, $data));
    }

    /**
     * @param UriInterface|string $defaultUri
     * @param array $queryParams
     * @return ResponseInterface
     */
    public function redirectTo($defaultUri, array $queryParams = []): ResponseInterface
    {
        if (isset($queryParams['redirect'])) {
            $uri = new Uri(urldecode($queryParams['redirect']));
        } else {
            $uri = $defaultUri;
        }

        return new RedirectResponse($uri);
    }

    /**
     * @param UserControllerEvent $e
     */
    public function onBeforeAccountUpdateFormValidation(UserControllerEvent $e)
    {
        // no-op
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

        /** @var ServerRequestInterface $request */
        $request = $request->withMethod('POST');
        $request = $request->withParsedBody($loginData);
        $request = $request->withUri($this->routeHelper
            ->generateUri($this->webAuthenticationOptions->getLoginRoute()));

        return $this->loginAction->process($request, $this->getHandler());
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
