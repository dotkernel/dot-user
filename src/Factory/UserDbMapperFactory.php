<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace Dot\User\Factory;

use Dot\Helpers\DependencyHelperTrait;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Mapper\UserDbMapper;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\HydratorInterface;
use Zend\ServiceManager\Exception\InvalidServiceException;

/**
 * Class UserDbMapperFactory
 * @package Dot\User\Factory
 */
class UserDbMapperFactory
{
    use DependencyHelperTrait;

    public function __invoke(ContainerInterface $container, $requestedName)
    {
        if(!class_exists($requestedName)) {
            throw new InvalidServiceException("Class of type $requestedName could not be found");
        }

        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $dbAdapter = $container->get($options->getDbOptions()->getDbAdapter());

        $prototype = $this->getDependencyObject($container, $options->getUserEntity());
        if(!$prototype instanceof UserEntityInterface) {
            throw new RuntimeException('User entity prototype not valid');
        }

        $hydrator = $this->getDependencyObject($container, $options->getUserEntityHydrator());
        if(!$hydrator instanceof HydratorInterface) {
            $hydrator = new ClassMethods(false);
        }

        /** @var UserDbMapper $mapper */
        $mapper = new $requestedName(
            $options->getDbOptions()->getUserTable(),
            $dbAdapter,
            $prototype,
            $hydrator
        );
        $mapper->setDbOptions($options->getDbOptions());

        return $mapper;
    }

}