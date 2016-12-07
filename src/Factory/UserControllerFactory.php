<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/23/2016
 * Time: 8:44 PM
 */

namespace Dot\User\Factory;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\User\Controller\UserController;
use Dot\User\Form\UserFormManager;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class UserControllerFactory
 * @package Dot\User\Factory
 */
class UserControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);

        $userService = $container->get('UserService');
        $controller = new UserController(
            $userService,
            $container->get(LoginAction::class),
            $options,
            $container->get(UserFormManager::class)
        );

        return $controller;
    }
}