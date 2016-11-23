<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/23/2016
 * Time: 7:55 PM
 */

namespace Dot\User\Factory\Form;

use Dot\Ems\Validator\NoRecordsExists;
use Dot\User\EventManagerAwareFactoryTrait;
use Dot\User\Form\InputFilter\RegisterInputFilter;
use Dot\User\Form\RegisterForm;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
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

        $filter = new RegisterInputFilter(
            $options,
            new NoRecordsExists([
                'service' => $container->get(UserServiceInterface::class),
                'key' => 'email'
            ]),
            new NoRecordsExists([
                'mapper' => $container->get(UserServiceInterface::class),
                'key' => 'username'
            ])
        );
        $filter->setEventManager($this->getEventManager($container));
        $filter->init();

        $form = new RegisterForm($options);
        $form->setInputFilter($filter);
        $form->setHydrator($container->get($options->getUserEntityHydrator()));
        $form->setEventManager($this->getEventManager($container));
        $form->init();

        return $form;
    }
}