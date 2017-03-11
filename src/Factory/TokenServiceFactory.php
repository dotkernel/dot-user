<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Event\TokenEventListenerInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenService;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class TokenServiceFactory
 * @package Dot\User\Factory
 */
class TokenServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return TokenService
     */
    public function __invoke(ContainerInterface $container, $requestedName): TokenService
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);

        /** @var TokenService $service */
        $service = new $requestedName($options);
        $service->attach($service->getEventManager(), 1000);

        if (isset($options->getEventListeners()['token']) && is_array($options->getEventListeners()['token'])) {
            $this->attachListeners($container, $options->getEventListeners()['token'], $service->getEventManager());
        }

        return $service;
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
     * @return TokenEventListenerInterface
     */
    protected function getListenerObject(ContainerInterface $container, string $listener): TokenEventListenerInterface
    {
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (!$listener instanceof TokenEventListenerInterface) {
            throw new RuntimeException('User event listener is not an instance of '
                . TokenEventListenerInterface::class);
        }

        return $listener;
    }
}
