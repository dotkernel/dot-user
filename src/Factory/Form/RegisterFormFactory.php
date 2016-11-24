<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/23/2016
 * Time: 7:55 PM
 */

namespace Dot\User\Factory\Form;


use Dot\User\EventManagerAwareFactoryTrait;
use Dot\User\Form\Fieldset\UserFieldset;
use Dot\User\Form\InputFilter\UserInputFilter;
use Dot\User\Form\RegisterForm;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class RegisterFormFactory
 * @package Dot\User\Factory\Form
 */
class RegisterFormFactory
{
    use EventManagerAwareFactoryTrait;

    /**
     * @param ContainerInterface $container
     * @return RegisterForm
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $moduleOptions */
        $options = $container->get(UserOptions::class);

        $filter = $container->get(UserInputFilter::class);
        $fieldset = $container->get(UserFieldset::class);

        $form = new RegisterForm($options, $fieldset, $filter);
        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}