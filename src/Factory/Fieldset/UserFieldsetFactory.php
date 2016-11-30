<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 11/24/2016
 * Time: 7:56 PM
 */

namespace Dot\User\Factory\Fieldset;


use Dot\User\Entity\UserEntityInterface;
use Dot\User\Exception\RuntimeException;
use Dot\User\Form\Fieldset\UserFieldset;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\HydratorInterface;

/**
 * Class UserFieldsetFactory
 * @package Dot\User\Factory\Fieldset
 */
class UserFieldsetFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);

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

        $fieldset = new UserFieldset();
        $fieldset->setObject($prototype);
        $fieldset->setHydrator($hydrator);
        $fieldset->init();

        return $fieldset;
    }
}