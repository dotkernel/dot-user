<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 9:29 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Controller\Plugin\PluginManager;
use Dot\User\Authentication\AuthenticationListener;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Interop\Container\ContainerInterface;

/**
 * Class BeforeAuthenticationListenerFactory
 * @package Dot\User\Factory
 */
class AuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container, string $requestedName): AuthenticationListener
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
