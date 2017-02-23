<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/3/2017
 * Time: 8:29 PM
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

/**
 * Class ResetTokenEntity
 * @package Dot\User\Entity
 */
class ResetTokenEntity extends AbstractTokenEntity
{
    public function getType(): string
    {
        return AbstractTokenEntity::TOKEN_RESET;
    }
}
