<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Controller\Plugin\PluginManager;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Psr\Container\ContainerInterface;

/**
 * Class BeforeAuthenticationListenerFactory
 * @package Dot\User\Factory
 */
class AuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName)
    {
        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $container->get(PluginManager::class);
        $formsPlugin = $controllerPluginManager->get('forms');
        $userOptions = $container->get(UserOptions::class);
        $userService = $container->get(UserServiceInterface::class);
        $tokenService = $container->get(TokenServiceInterface::class);

        return new $requestedName(
            $userService,
            $tokenService,
            $formsPlugin,
            $userOptions
        );
    }
}
