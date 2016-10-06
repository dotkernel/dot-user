<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/12/2016
 * Time: 11:04 PM
 */

namespace Dot\User\Middleware;

use Dot\Authentication\AuthenticationInterface;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class AutoLogin
 * @package Dot\User\Middleware
 */
class AutoLogin
{
    /** @var  UserOptions */
    protected $options;

    /** @var  FlashMessengerInterface */
    protected $flashMessenger;

    /** @var  UrlHelper */
    protected $urlHelper;

    /** @var  UserServiceInterface */
    protected $userService;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  ServerRequestInterface */
    protected $request;

    /**
     * AutoLogin constructor.
     * @param AuthenticationInterface $authentication
     * @param UserServiceInterface $userService
     * @param UrlHelper $urlHelper
     * @param FlashMessengerInterface $messenger
     * @param UserOptions $options
     */
    public function  __construct(
        AuthenticationInterface $authentication,
        UserServiceInterface $userService,
        UrlHelper $urlHelper,
        FlashMessengerInterface $messenger,
        UserOptions $options
    )
    {
        $this->authentication = $authentication;
        $this->userService = $userService;
        $this->urlHelper = $urlHelper;
        $this->flashMessenger = $messenger;
        $this->options = $options;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $this->request = $request;

        if(!$this->authentication->hasIdentity()) {
            $cookies = $request->getCookieParams();
            $key = $this->options->getLoginOptions()->getRememberMeCookieName();

            if(isset($cookies[$key])) {
                try {
                    $data = @unserialize(base64_decode($cookies[$key]));

                    if ($data) {
                        $selector = $data['selector'];
                        $token = $data['token'];

                        $r = $this->userService->checkRememberToken($selector, $token);
                        if ($r) {
                            $userId = (int)$r['userId'];
                            $user = $this->userService->findUser($userId);

                            if ($user) {
                                //renew the tokens
                                $this->userService->removeRememberToken($user);
                                $this->userService->generateRememberToken($user);

                                //autologin user
                                $this->authentication->setIdentity($user);
                            }
                        }
                        else {
                            $this->unsetRememberCookie($key);
                        }
                    }
                    else {
                        $this->unsetRememberCookie($key);
                    }
                }
                catch(\Exception $e) {
                    error_log("Auto-login error: " . $e->getMessage());
                    $this->unsetRememberCookie($key);
                }
            }
        }

        return $next($request, $response);
    }

    protected function unsetRememberCookie($key)
    {
        if(isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            setcookie($key, '', time() - 3600, '/');
        }
    }
}