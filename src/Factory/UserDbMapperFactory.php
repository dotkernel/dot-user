<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace Dot\User\Factory;

use Dot\User\Mapper\UserDbMapper;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;

/**
 * Class UserDbMapperFactory
 * @package Dot\User\Factory
 */
class UserDbMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $options */
        $options = $container->get(UserOptions::class);
        $dbAdapter = $container->get($options->getDbOptions()->getDbAdapter());

        $mapper = new UserDbMapper(
            $options->getDbOptions()->getUserTable(),
            $dbAdapter,
            $options->getDbOptions(),
            $container->get($options->getUserEntity()),
            $container->get($options->getUserEntityHydrator())
        );

        return $mapper;
    }

}