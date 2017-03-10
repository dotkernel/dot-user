<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/8/2017
 * Time: 4:51 AM
 */

declare(strict_types = 1);

namespace Dot\User\Mapper;

use Dot\Mapper\Mapper\MapperInterface;
use Dot\User\Entity\UserEntity;

/**
 * Interface UserMapperInterface
 * @package Dot\User\Mapper
 */
interface UserMapperInterface extends MapperInterface
{
    /**
     * @param string $email
     * @param array $options
     * @return UserEntity|null
     */
    public function getByEmail(string $email, array $options = []): ?UserEntity;
}
