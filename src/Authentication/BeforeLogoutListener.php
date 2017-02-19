<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 7:51 PM
 */

declare(strict_types = 1);

namespace Dot\User\Authentication;

use Dot\Authentication\Web\Event\AbstractAuthenticationEventListener;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\User\Service\TokenServiceInterface;

/**
 * Class AfterLogoutListener
 * @package Dot\User\Authentication
 */
class BeforeLogoutListener extends AbstractAuthenticationEventListener
{
    /** @var  TokenServiceInterface */
    protected $tokenService;

    /**
     * AfterLogoutListener constructor.
     * @param TokenServiceInterface $tokenService
     */
    public function __construct(TokenServiceInterface $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onLogout(AuthenticationEvent $e)
    {
        $authentication = $e->getAuthenticationService();
        $identity = $authentication->getIdentity();
        $this->tokenService->deleteRememberTokens(['userId' => $identity->getId()]);
    }
}
