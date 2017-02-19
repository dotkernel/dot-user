<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 9:26 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Controller\Plugin\PluginManager;
use Dot\User\Authentication\InjectLoginFormListener;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class InjectLoginFormListenerFactory
 * @package Dot\User\Factory
 */
class InjectLoginFormListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $container->get(PluginManager::class);
        $formsPlugin = $controllerPluginManager->get('forms');
        $userOptions = $container->get(UserOptions::class);

        return new InjectLoginFormListener($formsPlugin, $userOptions);
    }
}
