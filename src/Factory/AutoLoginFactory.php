<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\User\Middleware\AutoLogin;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Psr\Container\ContainerInterface;

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
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        return new $requestedName(
            $container->get(AuthenticationInterface::class),
            $container->get(UserServiceInterface::class),
            $container->get(TokenServiceInterface::class),
            $container->get(UserOptions::class)
        );
    }
}
