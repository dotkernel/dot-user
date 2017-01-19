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

        $fieldset->remove('id');
        $filter->remove('id');
        if (!$options->getRegisterOptions()->isEnableUsername()) {
            $fieldset->remove('username');
            $filter->remove('username');
        }

        $form = new RegisterForm($options, $fieldset);
        $form->getInputFilter()->add($filter, 'user');

        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}
