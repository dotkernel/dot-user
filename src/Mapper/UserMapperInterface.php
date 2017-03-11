<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
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
