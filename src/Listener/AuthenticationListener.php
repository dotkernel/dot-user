<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/21/2016
 * Time: 10:45 PM
 */

namespace Dot\User\Listener;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Action\LogoutAction;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Form\Form;

/**
 * Class AuthenticationListener
 * @package Dot\User\Listener
 */
class AuthenticationListener extends AbstractListenerAggregate
{
    /** @var  Form */
    protected $loginForm;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  UserOptions */
    protected $options;

    public function __construct(
        Form $form,
        FlashMessengerInterface $flashMessenger,
        UserServiceInterface $userService,
        UserOptions $options
    ) {
        $this->loginForm = $form;
        $this->flashMessenger = $flashMessenger;
        $this->userService = $userService;
        $this->options = $options;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $sharedEvents = $events->getSharedManager();

        //this will be called after the default prepare listener and the actual authentication call
        //use it for additional checks and data insertions into template
        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'injectData'],
            500);

        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'preAuthentication'],
            400);

        $this->listeners[] = $sharedEvents->attach(
            LoginAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATION_AUTHENTICATE,
            [$this, 'postAuthentication'],
            -50
        );

        $this->listeners[] = $sharedEvents->attach(
            LogoutAction::class,
            AuthenticationEvent::EVENT_AUTHENTICATION_LOGOUT,
            [$this, 'onLogout'],
            100
        );
    }

    /**
     * We'll use this to inject our form into the event, consequently into the template
     *
     * @param AuthenticationEvent $e
     */
    public function injectData(AuthenticationEvent $e)
    {
        $form = $this->loginForm;
        $data = $this->flashMessenger->getData('loginFormData') ?: [];
        $messages = $this->flashMessenger->getData('loginFormMessages') ?: [];

        $form->setData($data);
        $form->setMessages($messages);

        $e->setParam('form', $form);
        $e->setParam('passwordRecoveryEnabled', $this->options->getPasswordRecoveryOptions()
            ->isEnablePasswordRecovery());
        $e->setParam('showLabels', $this->options->isShowFormInputLabels());
    }

    /**
     * Pre authentication listener that happens before the default authentication
     * Can be used to prepare some data for the form and pre-validate data
     *
     * @param AuthenticationEvent $e
     */
    public function preAuthentication(AuthenticationEvent $e)
    {
        //we can leverage the default authentication flow
        //we will inject the form into the template, and validate data on POST
        //in case of errors, we set them on the event object
        $request = $e->getRequest();
        $form = $e->getParam('form', null);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            if ($form instanceof Form) {
                $form->setData($data);

                if (!$form->isValid()) {
                    $messages = [];
                    foreach ($form->getMessages() as $message) {
                        $messages[] = current($message);
                    }
                    $e->setError($messages);

                    $this->flashMessenger->addData('loginFormData', $data);
                    $this->flashMessenger->addData('loginFormMessages', $form->getMessages());
                    return;
                }

                $data = array_merge($e->getParams(), $data, $form->getData());
                $e->setParams($data);
            }
        }
    }

    /**
     * In case authentication result is invalid, store form data and messages in session
     *
     * @param AuthenticationEvent $e
     */
    public function postAuthentication(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        /** @var Form $form */
        $form = $e->getParam('form');

        if ($request->getMethod() === 'POST') {
            /** @var AuthenticationResult $result */
            $result = $e->getAuthenticationResult();
            if ($result && !$result->isValid()) {
                $data = $form->getData();
                $this->flashMessenger->addData('loginFormData', $data);
            } elseif ($result && $result->isValid()) {
                $user = $result->getIdentity();
                if (!$user instanceof UserEntityInterface) {
                    /** @var UserEntityInterface $user */
                    $user = $this->userService->find([$this->userService->getMapper()->getIdentifierName() => $user->getId()]);
                }

                //validate account status
                if ($this->options->isEnableUserStatus()) {
                    $status = $user->getStatus();

                    if ($status && !in_array($status, $this->options->getLoginOptions()->getAllowedLoginStatuses())) {
                        $data = $form->getData();
                        $this->flashMessenger->addData('loginFormData', $data);

                        $e->setError($this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_LOGIN_ACCOUNT_INACTIVE));

                        //clear identity <=> logout
                        $e->getAuthenticationService()->clearIdentity();
                        return;
                    }
                }

                //if remember me is checked, generate a token
                if ($this->options->getLoginOptions()->isEnableRememberMe()) {
                    $data = $form->getData();
                    if (isset($data['remember']) && $data['remember'] == 'yes') {
                        //generate and save token to backend storage
                        $result = $this->userService->generateRememberToken($user);
                        if (!$result->isValid()) {
                            $this->flashMessenger->addWarning($result->getMessage());
                        }
                    }
                }
            }
        }
    }

    public function onLogout(AuthenticationEvent $e)
    {
        //clear any remember tokens for this user
        /** @var AuthenticationInterface $authentication */
        $authentication = $e->getAuthenticationService();
        $user = $authentication->getIdentity();
        if (!$user instanceof UserEntityInterface) {
            /** @var UserEntityInterface $user */
            $user = $this->userService->find([$this->userService->getMapper()->getIdentifierName() => $user->getId()]);
        }

        $this->userService->removeRememberToken($user);
    }

    /**
     * @return Form
     */
    public function getLoginForm()
    {
        return $this->loginForm;
    }

    /**
     * @param Form $loginForm
     * @return AuthenticationListener
     */
    public function setLoginForm($loginForm)
    {
        $this->loginForm = $loginForm;
        return $this;
    }
}