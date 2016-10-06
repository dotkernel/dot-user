<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace Dot\User\Factory;

use Dot\User\Mapper\UserDbMapper;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\TableGateway\Feature\EventFeature;

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

        $resultSetPrototype = new HydratingResultSet(
            $container->get($options->getUserEntityHydrator()),
            $container->get($options->getUserEntity()));

        $mapper = new UserDbMapper(
            $options->getDbOptions()->getUserTable(),
            $options->getDbOptions(),
            $dbAdapter,
            null,
            $resultSetPrototype);

        return $mapper;
    }

}