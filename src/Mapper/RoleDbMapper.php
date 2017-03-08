<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/13/2017
 * Time: 9:20 PM
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
