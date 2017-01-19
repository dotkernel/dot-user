<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 12/7/2016
 * Time: 1:25 AM
 */

namespace Dot\User\Factory\Form;

use Dot\User\EventManagerAwareFactoryTrait;
use Dot\User\Form\Fieldset\UserFieldset;
use Dot\User\Form\InputFilter\UserInputFilter;
use Dot\User\Form\UserForm;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class UserFormFactory
 * @package Dot\User\Factory\Form
 */
class UserFormFactory
{
    use EventManagerAwareFactoryTrait;

    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);

        $fieldset = $container->get(UserFieldset::class);
        $filter = $container->get(UserInputFilter::class);

        //we have a separate action for changing password, so remove it from account update
        $fieldset->remove('password')->remove('passwordVerify');
        $filter->remove('password')->remove('passwordVerify');

        if (!$options->getRegisterOptions()->isEnableUsername()) {
            $fieldset->remove('username');
            $filter->remove('username');
        }

        $form = new UserForm($options, $fieldset);
        $form->getInputFilter()->add($filter, 'user');

        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}
