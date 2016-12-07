<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/21/2016
 * Time: 10:50 PM
 */

namespace Dot\User\Factory;

use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\User\Form\LoginForm;
use Dot\User\Form\UserFormManager;
use Dot\User\Listener\AuthenticationListener;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class AuthenticationListenerFactory
 * @package Dot\User\Factory
 */
class AuthenticationListenerFactory
{
    /**
     * @param ContainerInterface $container
     * @return AuthenticationListener
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserFormManager $formManager */
        $formManager = $container->get(UserFormManager::class);

        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        return new AuthenticationListener(
            $formManager->get(LoginForm::class),
            $container->get(FlashMessengerInterface::class),
            $container->get('UserService'),
            $options
        );
    }
}