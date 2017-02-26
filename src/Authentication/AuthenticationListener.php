<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 7:31 PM
 */

declare(strict_types = 1);

namespace Dot\User\Authentication;

use Dot\Authentication\AuthenticationInterface;
use Dot\Authentication\AuthenticationResult;
use Dot\Authentication\Web\Event\AbstractAuthenticationEventListener;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\User\Form\LoginForm;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;

/**
 * Class AfterAuthenticationListener
 * @package Dot\User\Authentication
 */
class AuthenticationListener extends AbstractAuthenticationEventListener
{
    /** @var  FormsPlugin */
    protected $formsPlugin;

    /** @var  UserOptions */
    protected $userOptions;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  TokenServiceInterface */
    protected $tokenService;

    /**
     * AfterAuthenticationListener constructor.
     * @param UserServiceInterface $userService
     * @param TokenServiceInterface $tokenService
     * @param FormsPlugin $formsPlugin
     * @param UserOptions $userOptions
     */
    public function __construct(
        UserServiceInterface $userService,
        TokenServiceInterface $tokenService,
        FormsPlugin $formsPlugin,
        UserOptions $userOptions
    ) {
        $this->formsPlugin = $formsPlugin;
        $this->userOptions = $userOptions;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onBeforeAuthentication(AuthenticationEvent $e)
    {
        /** @var LoginForm $form */
        $form = $e->getParam('form');
        $data = $e->getParam('data', []);

        $form->setData($data);
        if (!$form->isValid()) {
            $messages = $this->formsPlugin->getMessages($form);
            $e->setParam('error', $messages);
            $this->formsPlugin->saveState($form);
            return;
        }
        $e->setParam('data', $form->getData());
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onAfterAuthentication(AuthenticationEvent $e)
    {
        /** @var LoginForm $form */
        $form = $e->getParam('form');

        /** @var AuthenticationResult $result */
        $result = $e->getParam('authenticationResult');
        /** @var AuthenticationInterface $authenticationService */
        $authenticationService = $e->getParam('authenticationService');

        $identity = $result->getIdentity();
        $user = $this->userService->find($identity->getId(), [
            'conditions' => ['status' => $this->userOptions->getLoginOptions()->getAllowedStatus()]
        ]);

        if (!$user) {
            $this->formsPlugin->saveState($form);
            $e->setParam('error', $this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::ACCOUNT_LOGIN_STATUS_NOT_ALLOWED));

            $authenticationService->clearIdentity();
            return;
        }

        // sets the identity to the found user, gives us more details
        $authenticationService->setIdentity($user);

        //generate remember me token if active
        if ($this->userOptions->getLoginOptions()->isEnableRemember()) {
            $data = $form->getData();
            if (isset($data['remember']) && $data['remember'] == 'yes') {
                //we always delete any previous remember tokens before generating new one
                $this->tokenService->deleteRememberTokens(['userId' => $user->getId()]);
                $t = $this->tokenService->generateRememberToken($user);
                if (!$t->isValid()) {
                    error_log('Error generating remember me token for user id: ' . $user->getId());
                }
            }
        }
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onAuthenticationError(AuthenticationEvent $e)
    {
        /** @var LoginForm $form */
        $form = $e->getParam('form');
        $this->formsPlugin->saveState($form);
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onBeforeLogout(AuthenticationEvent $e)
    {
        /** @var AuthenticationInterface $authentication */
        $authentication = $e->getParam('authenticationService');
        $identity = $authentication->getIdentity();

        $this->tokenService->deleteRememberTokens(['userId' => $identity->getId()]);
    }
}
