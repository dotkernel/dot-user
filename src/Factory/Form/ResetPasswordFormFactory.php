<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/26/2016
 * Time: 9:06 PM
 */

namespace Dot\User\Factory\Form;

use Dot\User\EventManagerAwareFactoryTrait;
use Dot\User\Form\InputFilter\ResetPasswordInputFilter;
use Dot\User\Form\ResetPasswordForm;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class ResetPasswordFormFactory
 * @package Dot\User\Factory\Form
 */
class ResetPasswordFormFactory
{
    use EventManagerAwareFactoryTrait;

    /**
     * @param ContainerInterface $container
     * @return ResetPasswordForm
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(UserOptions::class);

        $filter = new ResetPasswordInputFilter($options);
        $filter->setEventManager($this->getEventManager($container));
        $filter->init();

        $form = new ResetPasswordForm();
        $form->setInputFilter($filter);
        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}