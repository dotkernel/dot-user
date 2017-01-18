<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 8:38 PM
 */

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
     * @return UserOptions
     */
    public function __invoke(ContainerInterface $container)
    {
        return new UserOptions($container->get('config')['dot_user']);
    }
}
