<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Doctrine\ORM\EntityManager;
use Dot\User\Entity\UserEntity;
use Dot\User\Event\UserEventListenerInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserService;
use Dot\User\Service\UserServiceInterface;
use Psr\Container\ContainerInterface;
use Zend\Crypt\Password\PasswordInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UserServiceFactory
 * @package Dot\User\Factory
 */
class UserDoctrineServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName): UserServiceInterface
    {
        /** @var EntityManager $em */
        $em = $container->get('doctrine.entity_manager.orm_default');

        /** @var TokenServiceInterface $tokenService */
        $tokenService = $container->get(TokenServiceInterface::class);
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);

        /** @var UserService $service */
        $service = new $requestedName(
            $tokenService,
            $container->get(PasswordInterface::class),
            $container->get(UserOptions::class)
        );

        $service->attach($service->getEventManager(), 1000);
        if ($tokenService instanceof EventManagerAwareInterface) {
            $service->attach($tokenService->getEventManager(), 500);
        }

        if (isset($options->getEventListeners()['user']) && is_array($options->getEventListeners()['user'])) {
            $this->attachListeners($container, $options->getEventListeners()['user'], $service->getEventManager());
        }

        $service->setEntityManager($em);

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
     * @return UserEventListenerInterface
     */
    protected function getListenerObject(ContainerInterface $container, string $listener): UserEventListenerInterface
    {
        if ($container->has($listener)) {
            $listener = $container->get($listener);
        }

        if (is_string($listener) && class_exists($listener)) {
            $listener = new $listener();
        }

        if (!$listener instanceof UserEventListenerInterface) {
            throw new RuntimeException('User event listener is not an instance of '
                . UserEventListenerInterface::class);
        }

        return $listener;
    }
}
