<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 10:11 PM
 */

namespace Dot\User\Controller;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Controller\AbstractActionController;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\FlashMessagesTrait;
use Dot\User\Form\ChangePasswordForm;
use Dot\User\Form\ForgotPasswordForm;
use Dot\User\Form\UserFormManager;
use Dot\User\Form\LoginForm;
use Dot\User\Form\RegisterForm;
use Dot\User\Form\ResetPasswordForm;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\ResultInterface;
use Dot\User\Result\UserOperationResult;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Uri;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;

/**
 * Class UserController
 * @package Dot\User\Controller
 */
class UserController extends AbstractActionController
{
    use FlashMessagesTrait;

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
    )
    {
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
        $this->userService->setRequest($this->getRequest());
        $this->userService->setResponse($this->getResponse());

        return parent::dispatch();
    }

    /**
     * @return RedirectResponse
     */
    public function confirmAccountAction()
    {
        if (!$this->options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
            $this->addError($this->options->getMessagesOptions()->getMessage(
                MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_DISABLED),
                $this->flashMessenger());

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        /** @var ResultInterface $result */
        $result = $this->userService->confirmAccount($email, $token);
        if (!$result->isValid()) {
            $this->addError($result->getMessages(), $this->flashMessenger());
        } else {
            $this->addSuccess($result->getMessages(), $this->flashMessenger());
        }

        return new RedirectResponse($this->urlHelper()->generate('login'));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function changePasswordAction()
    {
        $request = $this->getRequest();
        /** @var FlashMessengerPlugin $messenger */
        $messenger = $this->flashMessenger();

        /** @var Form $form */
        $form = $this->formManager->get(ChangePasswordForm::class);

        $data = $messenger->getData('changePasswordFormData') ?: [];
        $formMessages = $messenger->getData('changePasswordFormMessages') ?: [];

        //add session form data from previous request(PRG form)
        $form->setData($data);
        $form->setMessages($formMessages);

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            $isValid = $form->isValid();
            $data = $form->getData();

            //as we use PRG forms, store form data in session for next page display
            $messenger->addData('changePasswordFormData', $data);
            $messenger->addData('changePasswordFormMessages', $form->getMessages());

            if($isValid) {
                $oldPassword = $data['password'];
                $newPassword = $data['newPassword'];

                /** @var UserOperationResult $result */
                $result = $this->userService->changePassword($oldPassword, $newPassword);
                if($result->isValid()) {
                    $this->addSuccess($result->getMessages(), $messenger);
                    return $this->redirectTo($request->getUri(), $request->getQueryParams());
                }
                else {
                    $this->addError($result->getMessages(), $messenger);
                    return new RedirectResponse($request->getUri(), 303);
                }
            }
            else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages, $messenger);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()
            ->render('dk-user::change-password',
                ['form' => $form, 'showLabels' => $this->options->isShowFormInputLabels()]));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function registerAction()
    {
        $request = $this->getRequest();

        if(!$this->options->getRegisterOptions()->isEnableRegistration()) {
            return new HtmlResponse(
                $this->template()->render('dk-user::register',
                    ['enableRegistration' => false]));
        }

        /** @var FlashMessengerPlugin $messenger */
        $messenger = $this->flashMessenger();

        /** @var Form $form */
        $form = $this->formManager->get(RegisterForm::class);

        $data = $messenger->getData('registerFormData') ?: [];
        $formMessages = $messenger->getData('registerFormMessages') ?: [];

        //add session form data from previous request(PRG form)
        $form->setData($data);
        $form->setMessages($formMessages);

        if($request->getMethod() === 'POST')
        {
            $postData = $request->getParsedBody();
            $form->bind($this->userService->getUserEntityPrototype());
            $form->setData($postData);

            $isValid = $form->isValid();
            /** @var UserEntityInterface $data */
            $data = $form->getData();

            //as we use PRG forms, store form data in session for next page display
            $messenger->addData('registerFormData', $postData);
            $messenger->addData('registerFormMessages', $form->getMessages());
            
            if($isValid) {
                /** @var UserOperationResult $result */
                $result = $this->userService->register($data);
                if(!$result->isValid()) {
                    $this->addError($result->getMessages(), $messenger);
                    return new RedirectResponse($request->getUri(), 303);
                }
                else {
                    $user = $result->getUser();
                    if($this->options->getRegisterOptions()->isLoginAfterRegistration()) {
                        return $this->autoLoginUser($user, $postData['password']);
                    }
                    else
                    {
                        $this->addSuccess($result->getMessages(), $messenger);
                        return $this->redirectTo($this->urlHelper()->generate('login'), $request->getQueryParams());
                    }
                }
            }
            else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages, $messenger);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse(
            $this->template()->render('dk-user::register',
                [
                    'form' => $form,
                    'enableRegistration' => $this->options->getRegisterOptions()->isEnableRegistration(),
                    'showLabels' => $this->options->isShowFormInputLabels()
                ]));
    }

    /**
     * Show the reset password form, validate data
     *
     * @return HtmlResponse|RedirectResponse
     */
    public function resetPasswordAction()
    {
        /** @var FlashMessengerPlugin $messenger */
        $messenger = $this->flashMessenger();

        if(!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->addError($this->options->getMessagesOptions()->getMessage(
                MessagesOptions::MESSAGE_RESET_PASSWORD_DISABLED),
                $messenger);

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();
        $params = $request->getQueryParams();

        $email = isset($params['email']) ? $params['email'] : '';
        $token = isset($params['token']) ? $params['token'] : '';

        /** @var Form $form */
        $form = $this->formManager->get(ResetPasswordForm::class);

        $data = $messenger->getData('resetPasswordFormData') ?: [];
        $formMessages = $messenger->getData('resetPasswordFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($formMessages);

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            $isValid = $form->isValid();
            $data = $form->getData();

            $messenger->addData('resetPasswordFormData', $data);
            $messenger->addData('resetPasswordFormMessages', $form->getMessages());

            if($isValid) {
                $newPassword = $data['newPassword'];

                /** @var UserOperationResult $result */
                $result = $this->userService->resetPassword($email, $token, $newPassword);

                if(!$result->isValid()) {
                    $this->addError($result->getMessages(), $messenger);
                    return new RedirectResponse($request->getUri(), 303);
                }
                else {
                    $this->addSuccess($result->getMessages(), $messenger);
                    return $this->redirectTo($this->urlHelper()->generate('login'), $request->getQueryParams());
                }
            }
            else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages, $messenger);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()->render(
            'dk-user::reset-password',
            ['form' => $form, 'showLabels' => $this->options->isShowFormInputLabels()]));
    }

    /**
     * @return HtmlResponse|RedirectResponse
     */
    public function forgotPasswordAction()
    {
        /** @var FlashMessengerPlugin $messenger */
        $messenger = $this->flashMessenger();

        if(!$this->options->getPasswordRecoveryOptions()->isEnablePasswordRecovery()) {
            $this->addError($this->options->getMessagesOptions()->getMessage(
                MessagesOptions::MESSAGE_RESET_PASSWORD_DISABLED),
                $messenger);

            return new RedirectResponse($this->urlHelper()->generate('login'));
        }

        $request = $this->getRequest();

        /** @var Form $form */
        $form = $this->formManager->get(ForgotPasswordForm::class);

        $data = $messenger->getData('forgotPasswordFormData') ?: [];
        $formMessages = $messenger->getData('forgotPasswordFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($formMessages);

        if($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            $form->setData($data);
            $isValid = $form->isValid();
            $data = $form->getData();

            $messenger->addData('forgotPasswordFormData', $data);
            $messenger->addData('forgotPasswordFormMessages', $form->getMessages());

            if($isValid) {
                $email = $data['email'];

                /** @var UserOperationResult $result */
                $result = $this->userService->generateResetToken($email);
                if($result->isValid()) {
                    $this->addInfo($result->getMessages(), $messenger);
                    return $this->redirectTo($this->urlHelper()->generate('login'), $request->getQueryParams());
                }
                else {
                    $this->addError($result->getMessages(), $messenger);
                    return new RedirectResponse($request->getUri(), 303);
                }
            }
            else {
                $messages = $this->getFormMessages($form->getMessages());
                $this->addError($messages, $messenger);
                return new RedirectResponse($request->getUri(), 303);
            }
        }

        return new HtmlResponse($this->template()->render(
            'dk-user::forgot-password',
            ['form' => $form, 'showLabels' => false]));
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

        $form = $this->formManager->get(LoginForm::class);
        $form->init();
        $csrf = ['name' => '', 'value' => ''];
        foreach ($form->getElements() as $element) {
            if($element instanceof Csrf) {
                $csrf['name'] = $element->getName();
                $csrf['value'] = $element->getValue();
            }
        }

        $loginData = [
            'identity' => $user->getEmail(),
            'password' => $password,
            'remember' => 'no',
        ];

        if(!empty($csrf['name'])) {
            $loginData[$csrf['name']] = $csrf['value'];
        }

        $form->setData($loginData);

        $form->isValid();

        $request = $request->withParsedBody($form->getData())
            ->withUri(new Uri($this->urlHelper()->generate('login')));

        return $this->loginAction->triggerEvent(
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            $request->getParsedBody(),
            $request,
            $response);
    }

    /**
     * @param array $formMessages
     * @return array
     */
    protected function getFormMessages(array $formMessages)
    {
        $messages = [];
        foreach ($formMessages as $message) {
            if(is_array($message)) {
                foreach ($message as $m) {
                    if(is_string($m)) {
                        $messages[] = $m;
                    }
                    elseif(is_array($m)) {
                        $messages = array_merge($messages, $this->getFormMessages($message));
                        break;
                    }
                }
            }
        }

        return $messages;
    }

    /**
     * @param $defaultUri
     * @param array $queryParams
     * @return RedirectResponse
     */
    protected function redirectTo($defaultUri, $queryParams = [])
    {
        if(isset($queryParams['redirect'])) {
            $uri = new Uri(urldecode($queryParams['redirect']));
        }
        else {
            $uri = $defaultUri;
        }

        return new RedirectResponse($uri);
    }

}