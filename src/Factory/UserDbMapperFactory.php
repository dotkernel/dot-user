<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/17/2017
 * Time: 9:09 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\Ems\Factory\DbMapperFactory;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class UserDbMapperFactory
 * @package Dot\User\Factory
 */
class UserDbMapperFactory extends DbMapperFactory
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];
        $options += [
            'user_options' => $container->get(UserOptions::class)
        ];
        return parent::__invoke($container, $requestedName, $options);
    }
}
