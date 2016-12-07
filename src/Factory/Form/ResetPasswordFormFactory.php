<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
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
        $filter->init();

        $form = new ResetPasswordForm($options);
        $form->setInputFilter($filter);
        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}