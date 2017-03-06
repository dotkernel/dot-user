<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/16/2017
 * Time: 10:52 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Event\TokenEventListenerInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenService;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
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
        $events = $container->has(EventManagerInterface::class)
            ? $container->get(EventManagerInterface::class)
            : new EventManager();

        $service->setEventManager($events);
        $service->attach($events, 1000);

        if (isset($options->getEventListeners()['token']) && is_array($options->getEventListeners()['token'])) {
            $this->attachListeners($container, $options->getEventListeners()['token'], $events);
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
