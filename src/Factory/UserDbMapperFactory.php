<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace Dot\User\Factory;

use Dot\User\Entity\UserEntityInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Mapper\UserDbMapper;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\HydratorInterface;

/**
 * Class UserDbMapperFactory
 * @package Dot\User\Factory
 */
class UserDbMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $dbAdapter = $container->get($options->getDbOptions()->getDbAdapter());

        $prototype = $options->getUserEntity();
        if($container->has($prototype)) {
            $prototype = $container->get($prototype);
        }

        if(is_string($prototype) && class_exists($prototype)) {
            $prototype = new $prototype;
        }

        if(!$prototype instanceof UserEntityInterface) {
            throw new RuntimeException('User entity prototype not valid');
        }

        if(!$options->getUserEntityHydrator()) {
            $hydrator = new ClassMethods(false);
        }
        else {
            $hydrator = $options->getUserEntityHydrator();
            if($container->has($hydrator)) {
                $hydrator = $container->get($hydrator);
            }

            if(is_string($hydrator) && class_exists($hydrator)) {
                $hydrator = new $hydrator;
            }

            if(!$hydrator instanceof HydratorInterface) {
                throw new RuntimeException('Invalid user entity hydrator');
            }
        }

        $mapper = new UserDbMapper(
            $options->getDbOptions()->getUserTable(),
            $dbAdapter,
            $prototype,
            $hydrator
        );
        $mapper->setDbOptions($options->getDbOptions());

        return $mapper;
    }

}