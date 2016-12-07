<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 11/24/2016
 * Time: 7:56 PM
 */

namespace Dot\User\Factory\Fieldset;

use Dot\Helpers\DependencyHelperTrait;
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
    use DependencyHelperTrait;

    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);

        $prototype = $this->getDependencyObject($container, $options->getUserEntity());
        if(!$prototype instanceof UserEntityInterface) {
            throw new RuntimeException('User entity prototype not valid');
        }

        $hydrator = $this->getDependencyObject($container, $options->getUserEntityHydrator());
        if(!$hydrator instanceof HydratorInterface) {
            $hydrator = new ClassMethods(false);
        }

        $fieldset = new UserFieldset();
        $fieldset->setObject($prototype);
        $fieldset->setHydrator($hydrator);
        $fieldset->init();

        return $fieldset;
    }
}