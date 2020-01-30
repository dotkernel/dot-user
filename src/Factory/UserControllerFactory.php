<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\Authentication\Web\Options\WebAuthenticationOptions;
use Dot\Helpers\Route\RouteHelper;
use Dot\User\Controller\UserController;
use Dot\User\Event\UserControllerEventListenerInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManagerInterface;

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
            $container->get(WebAuthenticationOptions::class),
            $container->get(RouteHelper::class),
            $container->get(LoginAction::class)
        );

        $controller->attach($controller->getEventManager(), 1000);

        if (isset($options->getEventListeners()['controller'])
            && is_array($options->getEventListeners()['controller'])
        ) {
            $this->attachListeners(
                $container,
                $options->getEventListeners()['controller'],
                $controller->getEventManager()
            );
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
