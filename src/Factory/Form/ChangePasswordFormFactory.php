<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/20/2016
 * Time: 4:44 PM
 */

namespace Dot\User\Factory\Form;

use Dot\User\EventManagerAwareFactoryTrait;
use Dot\User\Form\ChangePasswordForm;
use Dot\User\Form\InputFilter\ChangePasswordInputFilter;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class ChangePasswordFormFactory
 * @package Dot\User\Factory\Form
 */
class ChangePasswordFormFactory
{
    use EventManagerAwareFactoryTrait;

    /**
     * @param ContainerInterface $container
     * @return ChangePasswordForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(UserOptions::class);

        $filter = new ChangePasswordInputFilter($options);
        $filter->setEventManager($this->getEventManager($container));
        $filter->init();

        $form = new ChangePasswordForm($options);
        $form->setInputFilter($filter);
        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}