<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/16/2017
 * Time: 9:42 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class UserOptionsFactory
 * @package Dot\User\Factory
 */
class UserOptionsFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return UserOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName($container->get('config')['dot_user']);
    }
}
