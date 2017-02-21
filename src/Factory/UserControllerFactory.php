<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/16/2017
 * Time: 9:03 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Authentication\Web\Action\LoginAction;
use Dot\User\Controller\UserController;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
use Interop\Container\ContainerInterface;

/**
 * Class UserControllerFactory
 * @package Dot\User\Factory
 */
class UserControllerFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName): UserController
    {
        return new $requestedName(
            $container->get(UserServiceInterface::class),
            $container->get(UserOptions::class),
            $container->get(LoginAction::class)
        );
    }
}
