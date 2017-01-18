<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/21/2016
 * Time: 10:59 PM
 */

namespace Dot\User\Factory;

use Dot\User\Listener\AuthenticationListener;
use Dot\User\Middleware\Bootstrap;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class BootstrapFactory
 * @package Dot\User\Factory
 */
class BootstrapFactory
{
    /**
     * @param ContainerInterface $container
     * @return Bootstrap
     */
    public function __invoke(ContainerInterface $container)
    {
        $bootstrap = new Bootstrap();
        $eventManager = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $authenticationListener = $container->get(AuthenticationListener::class);
        $bootstrap->setEventManager($eventManager);
        $bootstrap->setAuthenticationListener($authenticationListener);

        return $bootstrap;
    }
}
