<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/21/2016
 * Time: 10:55 PM
 */

namespace Dot\User\Middleware;

use Dot\User\Listener\AuthenticationListener;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class Bootstrap
 * @package Dot\User\Middleware
 */
class Bootstrap
{
    use EventManagerAwareTrait;

    /** @var  AuthenticationListener */
    protected $authenticationListener;

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if ($this->authenticationListener) {
            $this->authenticationListener->attach($this->getEventManager());
        }

        return $next($request, $response);
    }

    /**
     * @return AuthenticationListener
     */
    public function getAuthenticationListener()
    {
        return $this->authenticationListener;
    }

    /**
     * @param AuthenticationListener $authenticationListener
     * @return Bootstrap
     */
    public function setAuthenticationListener($authenticationListener)
    {
        $this->authenticationListener = $authenticationListener;
        return $this;
    }
}