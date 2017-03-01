<?php
/**
 * @copyright: DotKernel
 * @library: dot-user
 * @author: n3vrax
 * Date: 2/22/2017
 * Time: 8:47 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Entity\UserEntity;
use Dot\User\Exception\RuntimeException;
use Dot\User\Form\UserFieldset;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\HydratorPluginManager;

/**
 * Class UserFieldsetFactory
 * @package Dot\User\Factory
 */
class UserFieldsetFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return UserFieldset
     */
    public function __invoke(ContainerInterface $container, $requestedName): UserFieldset
    {
        /** @var UserOptions $userOptions */
        $userOptions = $container->get(UserOptions::class);
        /** @var HydratorPluginManager $hydratorManager */
        $hydratorManager = $container->get('HydratorManager');

        /** @var UserFieldset $fieldset */
        $fieldset = new $requestedName();

        $entity = $userOptions->getUserEntity();
        if ($container->has($entity)) {
            $entity = $container->get($entity);
        }

        if (is_string($entity) && class_exists($entity)) {
            $entity = new $entity();
        }

        if (!$entity instanceof UserEntity) {
            throw new RuntimeException('User entity class must be an instance of ' . UserEntity::class);
        }

        $hydrator = $hydratorManager->get($entity->hydrator());

        $fieldset->setObject($entity);
        $fieldset->setHydrator($hydrator);
        $fieldset->setUserOptions($userOptions);

        return $fieldset;
    }
}
