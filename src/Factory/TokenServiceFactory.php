<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/16/2017
 * Time: 10:52 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class TokenServiceFactory
 * @package Dot\User\Factory
 */
class TokenServiceFactory
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
        $options['event_manager'] = $container->get(EventManagerInterface::class);

        return new $requestedName($options);
    }
}
