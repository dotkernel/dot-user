<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/3/2017
 * Time: 8:27 PM
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

/**
 * Class RememberTokenEntity
 * @package Dot\User\Entity
 */
class RememberTokenEntity extends AbstractSelectorTokenEntity
{
    public function getType(): string
    {
        return AbstractTokenEntity::TOKEN_REMEMBER;
    }
}
