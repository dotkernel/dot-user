<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 9:18 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\User\Middleware\AutoLogin;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Interop\Container\ContainerInterface;

/**
 * Class AutoLoginFactory
 * @package Dot\User\Factory
 */
class AutoLoginFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return AutoLogin
     */
    public function __invoke(ContainerInterface $container, string $requestedName): AutoLogin
    {
        return new $requestedName(
            $container->get(AuthenticationInterface::class),
            $container->get(UserServiceInterface::class),
            $container->get(TokenServiceInterface::class),
            $container->get(UserOptions::class)
        );
    }
}
