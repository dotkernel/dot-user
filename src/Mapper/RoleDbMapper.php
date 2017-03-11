<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Mapper;

use Dot\Mapper\Mapper\AbstractDbMapper;

/**
 * Class RoleDbMapper
 * @package Dot\User\Mapper
 */
class RoleDbMapper extends AbstractDbMapper
{
    protected $table = 'user_role';
}
