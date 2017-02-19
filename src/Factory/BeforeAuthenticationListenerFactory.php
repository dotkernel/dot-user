<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 9:33 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Controller\Plugin\PluginManager;
use Dot\User\Authentication\BeforeAuthenticationListener;
use Interop\Container\ContainerInterface;

/**
 * Class BeforeAuthenticationListenerFactory
 * @package Dot\User\Factory
 */
class BeforeAuthenticationListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var PluginManager $controllerPluginManager */
        $controllerPluginManager = $container->get(PluginManager::class);
        $formsPlugin = $controllerPluginManager->get('forms');

        return new BeforeAuthenticationListener($formsPlugin);
    }
}
