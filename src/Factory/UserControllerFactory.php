<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/16/2017
 * Time: 9:03 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Helpers\Route\RouteOptionHelper;
use Dot\User\Controller\UserController;
use Dot\User\Event\UserControllerEventListenerInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UserControllerFactory
 * @package Dot\User\Factory
 */
class UserControllerFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName): UserController
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);

        /** @var UserController $controller */
        $controller = new $requestedName(
            $container->get(UserServiceInterface::class),
            $options,
            $container->get(RouteOptionHelper::class),
            $container->get(LoginAction::class)
        );

        $em = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();
        $controller->setEventManager($em);
        $controller->attach($em, 1000);

        if (isset($options->getEventListeners()['controller'])
            && is_array($options->getEventListeners()['controller'])
        ) {
            $this->attachListeners($container, $options->getEventListeners()['controller'], $em);
        }

        return $controller;
    }

    /**
     * @param ContainerInterface $container
     * @param array $listeners
     * @param EventManagerInterface $em
     */
    protected function attachListeners(ContainerInterface $container, array $listeners, EventManagerInterface $em)
    {
        foreach ($listeners as $listener) {
            if (is_string($listener)) {
                $l = $this->getListenerObject($container, $listener);
                $p = 1;
                $l->attach($em, $p);
            } elseif (is_array($listener)) {
                $l = $listener['type'] ?? '';
                $p = $listener['priority'] ?? 1;
                $l = $this->getListenerObject($container, $l);
                $l->attach($em, $p);
            }
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string $listener
     * @return UserControllerEventListenerInterface
     */
    protected function getListenerObject(
        ContainerInterface $container,
        string $listener
    ): UserControllerEventListenerInterface {
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (!$listener instanceof UserControllerEventListenerInterface) {
            throw new RuntimeException('Controller event listener is not an instance of '
                . UserControllerEventListenerInterface::class);
        }

        return $listener;
    }
}
