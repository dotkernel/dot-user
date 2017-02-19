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
use Dot\User\Authentication\AfterAuthenticationListener;
use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserServiceInterface;
use Interop\Container\ContainerInterface;

/**
 * Class BeforeAuthenticationListenerFactory
 * @package Dot\User\Factory
 */
class AfterAuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $container->get(PluginManager::class);
        $formsPlugin = $controllerPluginManager->get('forms');
        $userOptions = $container->get(UserOptions::class);
        $userService = $container->get(UserServiceInterface::class);
        $tokenService = $container->get(TokenServiceInterface::class);

        return new AfterAuthenticationListener(
            $userService,
            $tokenService,
            $formsPlugin,
            $userOptions
        );
    }
}
