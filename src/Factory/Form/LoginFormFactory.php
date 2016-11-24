<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/23/2016
 * Time: 3:50 PM
 */

namespace Dot\User\Factory\Form;

use Dot\User\EventManagerAwareFactoryTrait;
use Dot\User\Form\InputFilter\LoginInputFilter;
use Dot\User\Form\LoginForm;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class LoginFormFactory
 * @package Dot\User\Factory\Form
 */
class LoginFormFactory
{
    use EventManagerAwareFactoryTrait;

    /**
     * @param ContainerInterface $container
     * @return LoginForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(UserOptions::class);

        $filter = new LoginInputFilter($options);
        $filter->init();

        $form = new LoginForm($options);
        $form->setInputFilter($filter);
        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}