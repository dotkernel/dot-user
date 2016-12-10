<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 10:11 PM
 */

namespace Dot\User\Controller;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Controller\AbstractActionController;
use Dot\Helpers\FormMessagesHelperTrait;
use Dot\Helpers\Psr7\HttpMessagesAwareInterface;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Form\ChangePasswordForm;
use Dot\User\Form\ForgotPasswordForm;
use Dot\User\Form\LoginForm;
use Dot\User\Form\RegisterForm;
use Dot\User\Form\ResetPasswordForm;
use Dot\User\Form\UserForm;
use Dot\User\Form\UserFormManager;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\ResultInterface;
use Dot\User\Result\UserOperationResult;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Form\Form;

/**
 * Class UserController
 * @package Dot\User\Controller
 */
class UserController extends AbstractActionController
{
    use FormMessagesHelperTrait;

    const LOGIN_ROUTE_NAME = 'login';

    /** @var  UserOptions */
    protected $options;

    /** @var  LoginAction */
    protected $loginAction;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  UserFormManager */
    protected $formManager;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param LoginAction $loginAction
     * @param UserOptions $options
     * @param UserFormManager $formManager
     */
    public function __construct(
        UserServiceInterface $userService,
        LoginAction $loginAction,
        UserOptions $options,
        UserFormManager $formManager
    ) {
        $this->userService = $userService;
        $this->options = $options;
        $this->loginAction = $loginAction;
        $this->formManager = $formManager;
    }

    /**
     * @return mixed
     */
    public function dispatch()
    {
        //set request/response object on user service for each request
        if($this->userService instanceof HttpMessagesAwareInterface) {
            $this->userService->setServerRequest($this->getRequest());
            $this->userService->setResponse($this->getResponse());
        }

        return parent::dispatch();
    }

    /**
     * @return RedirectResponse
     */
    public function confirmAccountAction()
    {
        if (!$this->options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
            $this->addError($this->options->getMessagesOptions()->getMessage(
                MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_DISABLED));

            return new RedirectResponse($this->url()->generate(self::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        /** @var ResultInterface $result */
        $result = $this->userService->confirmAccount($email, $token);
        if (!$result->isValid()) {
            $this->addError($result->getMessages());
        } else {
            $this->addSuccess($result->getMessages());
        }

        return new RedirectResponse($this->url()->generate(self::LOGIN_ROUTE_NAME));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function changePasswordAction()
    {
        $request = $this->getRequest();

        /** @var Form $form */
        $form = $this->formManager->get(ChangePasswordForm::class);

        $data = $this->flashMessenger()->getData('changePasswordFormData') ?: [];
        $formMessages = $this->flashMessenger()->getData('changePasswordFormMessages') ?: [];

        //add session form data from previous request(PRG form)
        $form->setData($data);
        $form->setMessages($formMessages);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            $isValid = $form->isValid();
            $data = $form->getData();

            //as we use PRG forms, store form data in session for next page display
            $this->flashMessenger()->addData('changePasswordFormData', $data);
            $this->flashMessenger()->addData('changePasswordFormMessages', $form->getMessages());

            if ($isValid) {
                $oldPassword = $data['password'];
                $newPassword = $data['newPassword'];

                /** @var UserOperationResult $result */
                $result = $this->userService->changePassword($oldPassword, $newPassword);
                if ($result->isValid()) {
                    $this->addSuccess($result->getMessages());
                    return $this->redirectTo($request->getUri(), $request->getQueryParams());
                } else {
                    $this->addError($result->getMessages());
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()
            ->render($this->options->getTemplateOptions()->getChangePasswordTemplate(),
                [
                    'form' => $form,
                    'showLabels' => $this->options->isShowFormInputLabels(),
                    'layoutTemplate' => $this->options->getTemplateOptions()->getChangePasswordTemplateLayout()
                ]));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function registerAction()
    {
        $request = $this->getRequest();

        if (!$this->options->getRegisterOptions()->isEnableRegistration()) {
            $this->addError($this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REGISTER_DISABLED));

            return new RedirectResponse($this->url()->generate(self::LOGIN_ROUTE_NAME));
        }

        /** @var Form $form */
        $form = $this->formManager->get(RegisterForm::class);

        $data = $this->flashMessenger()->getData('registerFormData') ?: [];
        $formMessages = $this->flashMessenger()->getData('registerFormMessages') ?: [];

        //add session form data from previous request(PRG form)
        $form->setData($data);
        $form->setMessages($formMessages);

        if ($request->getMethod() === 'POST') {
            $postData = $request->getParsedBody();
            $form->bind($this->userService->getMapper()->getPrototype());
            $form->setData($postData);

            $isValid = $form->isValid();
            /** @var UserEntityInterface $data */
            $data = $form->getData();

            //as we use PRG forms, store form data in session for next page display
            $this->flashMessenger()->addData('registerFormData', $postData);
            $this->flashMessenger()->addData('registerFormMessages', $form->getMessages());

            if ($isValid) {
                /** @var UserOperationResult $result */
                $result = $this->userService->register($data);
                if (!$result->isValid()) {
                    $this->addError($result->getMessages());
                    return new RedirectResponse($request->getUri(), 303);
                } else {
                    $user = $result->getUser();
                    if ($this->options->getRegisterOptions()->isLoginAfterRegistration()) {
                        return $this->autoLoginUser($user, $postData['password']);
                    } else {
                        $this->addSuccess($result->getMessages());
                        return $this->redirectTo($this->url()->generate(self::LOGIN_ROUTE_NAME),
                            $request->getQueryParams());
                    }
                }
            } else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template()->render($this->options->getTemplateOptions()->getRegisterTemplate(),
                [
                    'form' => $form,
                    'enableRegistration' => $this->options->getRegisterOptions()->isEnableRegistration(),
                    'showLabels' => $this->options->isShowFormInputLabels(),
                    'layoutTemplate' => $this->options->getTemplateOptions()->getRegisterTemplateLayout()
                ]));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     * @throws \Exception
     */
    public function accountAction()
    {
        $request = $this->getRequest();

        /** @var UserForm $form */
        $form = $this->formManager->get(UserForm::class);

        /** @var UserEntityInterface $identity */
        $identity = $this->authentication()->getIdentity();
        $user = $this->userService->find([$this->userService->getMapper()->getIdentifierName() => $identity->getId()]);

        //this should never happen, that's why we treat it as exception
        if(!$user instanceof UserEntityInterface) {
            throw new RuntimeException('Could not load user entity for identity ID');
        }

        $form->bind($user);

        /**
         * Get previous form data stored in session, to re-display the information and/or errors
         */
        $userFormData = $this->flashMessenger()->getData('userFormData') ?: [];
        $userFormMessages = $this->flashMessenger()->getData('userFormMessages') ?: [];

        $form->setData($userFormData);
        $form->setMessages($userFormMessages);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            //in case username is changed we need to check its uniqueness
            //but only in case username was actually changed from the previous one
            if (isset($data['user']['username']) && $data['user']['username'] === $user->getUsername()) {
                //consider we don't want to change username, remove the uniqueness check
                $form->removeUsernameValidation();
                $form->applyValidationGroup();
            }

            if (isset($data['user']['email']) && $data['user']['email'] === $user->getEmail()) {
                //consider we don't want to change email, remove the uniqueness check
                $form->removeEmailValidation();
                $form->applyValidationGroup();
            }

            $form->setData($data);

            $isValid = $form->isValid();

            //add form data and messages to the session, in case we do a PRG redirect
            $this->flashMessenger()->addData('userFormData', $data);
            $this->flashMessenger()->addData('userFormMessages', $form->getMessages());

            if ($isValid) {
                /** @var UserEntityInterface $user */
                $user = $form->getData();

                /** @var UserOperationResult $result */
                $result = $this->userService->updateAccount($user);

                if ($result->isValid()) {
                    $this->addSuccess($result->getMessages());
                    return new RedirectResponse($request->getUri());
                } else {
                    $this->addError($result->getMessages());
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $this->addError($this->getFormMessages($form->getMessages()));
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()->render(
            $this->options->getTemplateOptions()->getAccountTemplate(),
            [
                'form' => $form,
                'showLabels' => $this->options->isShowFormInputLabels(),
                'layoutTemplate' => $this->options->getTemplateOptions()->getAccountTemplateLayout()
            ]));
    }

    /**
     * Show the reset password form, validate data
     *
     * @return HtmlResponse|RedirectResponse
     */
    public function resetPasswordAction()
    {
        if (!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->addError($this->options->getMessagesOptions()->getMessage(
                MessagesOptions::MESSAGE_RESET_PASSWORD_DISABLED));

            return new RedirectResponse($this->url()->generate(self::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        /** @var Form $form */
        $form = $this->formManager->get(ResetPasswordForm::class);

        $data = $this->flashMessenger()->getData('resetPasswordFormData') ?: [];
        $formMessages = $this->flashMessenger()->getData('resetPasswordFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($formMessages);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            $isValid = $form->isValid();
            $data = $form->getData();

            $this->flashMessenger()->addData('resetPasswordFormData', $data);
            $this->flashMessenger()->addData('resetPasswordFormMessages', $form->getMessages());

            if ($isValid) {
                $newPassword = $data['newPassword'];

                /** @var UserOperationResult $result */
                $result = $this->userService->resetPassword($email, $token, $newPassword);

                if (!$result->isValid()) {
                    $this->addError($result->getMessages());
                    return new RedirectResponse($request->getUri(), 303);
                } else {
                    $this->addSuccess($result->getMessages());
                    return $this->redirectTo($this->url()->generate(self::LOGIN_ROUTE_NAME),
                        $request->getQueryParams());
                }
            } else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()->render(
            $this->options->getTemplateOptions()->getResetPasswordTemplate(),
            [
                'form' => $form,
                'showLabels' => $this->options->isShowFormInputLabels(),
                'layoutTemplate' => $this->options->getTemplateOptions()->getResetPasswordTemplateLayout()
            ]));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function forgotPasswordAction()
    {
        if (!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->addError($this->options->getMessagesOptions()->getMessage(
                MessagesOptions::MESSAGE_RESET_PASSWORD_DISABLED));

            return new RedirectResponse($this->url()->generate(self::LOGIN_ROUTE_NAME));
        }

        $request = $this->getRequest();

        /** @var Form $form */
        $form = $this->formManager->get(ForgotPasswordForm::class);

        $data = $this->flashMessenger()->getData('forgotPasswordFormData') ?: [];
        $formMessages = $this->flashMessenger()->getData('forgotPasswordFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($formMessages);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            $isValid = $form->isValid();
            $data = $form->getData();

            $this->flashMessenger()->addData('forgotPasswordFormData', $data);
            $this->flashMessenger()->addData('forgotPasswordFormMessages', $form->getMessages());

            if ($isValid) {
                $email = $data['email'];

                /** @var UserOperationResult $result */
                $result = $this->userService->generateResetToken($email);
                if ($result->isValid()) {
                    $this->addInfo($result->getMessages());
                    return $this->redirectTo($this->url()->generate(self::LOGIN_ROUTE_NAME),
                        $request->getQueryParams());
                } else {
                    $this->addError($result->getMessages());
                    return new RedirectResponse($request->getUri(), 303);
                }
            } else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()->render(
            $this->options->getTemplateOptions()->getForgotPasswordTemplate(),
            [
                'form' => $form,
                'showLabels' => false,
                'layoutTemplate' => $this->options->getTemplateOptions()->getForgotPasswordTemplateLayout()
            ]));
    }

    /**
     * Force an auth event using the LoginAction to automatically login the user after registration
     *
     * @param UserEntityInterface $user
     * @param $password
     * @return mixed
     */
    protected function autoLoginUser(UserEntityInterface $user, $password)
    {
        /** @var ServerRequestInterface $request */
        $request = $this->getRequest();
        $response = $this->getResponse();

        /** @var Form $form */
        $form = $this->formManager->get(LoginForm::class);
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
            ->withUri(new Uri($this->url()->generate(self::LOGIN_ROUTE_NAME)));

        return $this->loginAction->triggerAuthenticateEvent($request, $response, $request->getParsedBody());
    }

    /**
     * @param $defaultUri
     * @param array $queryParams
     * @return RedirectResponse
     */
    protected function redirectTo($defaultUri, $queryParams = [])
    {
        if (isset($queryParams['redirect'])) {
            $uri = new Uri(urldecode($queryParams['redirect']));
        } else {
            $uri = $defaultUri;
        }

        return new RedirectResponse($uri);
    }

    /** helpers to add messages into the FlashMessenger */

    /**
     * @param array|string $messages
     */
    public function addError($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addError($message);
        }
    }

    /**
     * @param array|string $messages
     */
    public function addInfo($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addInfo($message);
        }
    }

    /**
     * @param array|string $messages
     */
    public function addWarning($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addWarning($message);
        }
    }

    /**
     * @param array|string $messages
     */
    public function addSuccess($messages)
    {
        $messages = (array)$messages;
        foreach ($messages as $message) {
            $this->flashMessenger()->addSuccess($message);
        }
    }

}