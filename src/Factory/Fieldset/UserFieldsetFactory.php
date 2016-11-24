<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 11/24/2016
 * Time: 7:56 PM
 */

namespace Dot\User\Factory\Fieldset;


use Dot\User\Form\Fieldset\UserFieldset;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

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

        $fieldset = new UserFieldset();
        $fieldset->setObject($container->get($options->getUserEntity()));
        $fieldset->setHydrator($container->get($options->getUserEntityHydrator()));
        $fieldset->init();

        return $fieldset;
    }
}