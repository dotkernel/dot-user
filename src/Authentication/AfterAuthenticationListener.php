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
class AfterAuthenticationListener extends AbstractAuthenticationEventListener
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
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onAuthenticate(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        /** @var LoginForm $form */
        $form = $e->getParam('form');

        if ($request->getMethod() === 'POST') {
            $result = $e->getAuthenticationResult();
            if ($result && $result->isValid()) {
                $identity = $result->getIdentity();
                $user = $this->userService->find($identity->getId());

                if ($this->userOptions->isEnableUserStatus()) {
                    $status = $user->getStatus();

                    if ($status && !in_array($status, $this->userOptions->getLoginOptions()->getAllowedLoginStatus())) {
                        $this->formsPlugin->saveState($form);
                        $e->setError($this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::ACCOUNT_INACTIVE));

                        $e->getAuthenticationService()->clearIdentity();
                        return;
                    }
                }

                //generate remember me token if active
                if ($this->userOptions->getLoginOptions()->isEnableRemember()) {
                    $data = $form->getData();
                    if (isset($data['remember']) && $data['remember'] == 'yes') {
                        $t = $this->tokenService->generateRememberToken($user);
                        if (!$t->isValid()) {
                            error_log('Error generating remember me token for user id: ' . $user->getId());
                        }
                    }
                }
            } else {
                $this->formsPlugin->saveState($form);
            }
        }
    }
}
