<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 8:05 PM
 */

declare(strict_types = 1);

namespace Dot\User\Middleware;

use Dot\Authentication\AuthenticationInterface;
use Dot\User\Entity\RememberTokenEntity;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AutoLogin
 * @package Dot\User\Middleware
 */
class AutoLogin
{
    /** @var  UserOptions */
    protected $userOptions;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  TokenServiceInterface */
    protected $tokenService;

    /** @var  AuthenticationInterface */
    protected $authenticationService;

    /** @var  ServerRequestInterface */
    protected $request;

    /**
     * AutoLogin constructor.
     * @param AuthenticationInterface $authentication
     * @param UserServiceInterface $userService
     * @param TokenServiceInterface $tokenService
     * @param UserOptions $userOptions
     */
    public function __construct(
        AuthenticationInterface $authentication,
        UserServiceInterface $userService,
        TokenServiceInterface $tokenService,
        UserOptions $userOptions
    ) {
        $this->authenticationService = $authentication;
        $this->userService = $userService;
        $this->tokenService = $tokenService;
        $this->userOptions = $userOptions;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface {
        $this->request = $request;
        if ($this->userOptions->getLoginOptions()->isEnableRemember() && !$this->authenticationService->hasIdentity()) {
            $cookies = $request->getCookieParams();
            $key = $this->userOptions->getLoginOptions()->getRememberCookieName();

            if (isset($cookies[$key])) {
                try {
                    $data = @unserialize(base64_decode($cookies[$key]));
                    if ($data) {
                        $selector = $data['selector'];
                        $token = $data['token'];

                        $r = $this->tokenService->validateRememberToken($selector, $token);
                        if ($r->isValid()) {
                            /** @var RememberTokenEntity $token */
                            $token = $r->getParam('token');
                            $userId = (int)$token->getUserId();
                            $user = $this->userService->find($userId);
                            if ($user) {
                                // renew tokens
                                $this->tokenService->deleteRememberTokens(['userId' => $userId]);
                                $this->tokenService->generateRememberToken($user);

                                //auto-login user
                                $this->authenticationService->setIdentity($user);
                            }
                        } else {
                            $this->unsetRememberCookie($key);
                        }
                    } else {
                        $this->unsetRememberCookie($key);
                    }
                } catch (\Exception $e) {
                    error_log('Auto-login error: ' . $e->getMessage());
                    $this->unsetRememberCookie($key);
                }
            }
        }

        return $next($request, $response);
    }

    /**
     * @param $key
     */
    protected function unsetRememberCookie(string $key)
    {
        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            setcookie($key, '', time() - 3600, '/');
        }
    }
}
