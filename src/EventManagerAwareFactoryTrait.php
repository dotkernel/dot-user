<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/9/2016
 * Time: 10:26 PM
 */

namespace Dot\User;

use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class EventManagerAwareFactoryTrait
 * @package Dot\User
 */
trait EventManagerAwareFactoryTrait
{
    /**
     * @param ContainerInterface $container
     * @return mixed|EventManager
     */
    public function getEventManager(ContainerInterface $container)
    {
        $events = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();
        return $events;
    }
}
