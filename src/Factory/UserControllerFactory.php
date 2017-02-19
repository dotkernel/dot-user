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
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $options = [];
        $options['user_options'] = $container->get(UserOptions::class);
        $options['user_service'] = $container->get(UserServiceInterface::class);
        $options['login_action'] = $container->get(LoginAction::class);

        return new $requestedName($options);
    }
}
