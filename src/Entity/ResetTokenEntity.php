<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

/**
 * Class ResetTokenEntity
 * @package Dot\User\Entity
 */
class ResetTokenEntity extends AbstractTokenEntity implements \JsonSerializable
{
    public function getType(): string
    {
        return AbstractTokenEntity::TOKEN_RESET;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'token' => $this->getToken(),
            'expire' => $this->getExpire(),
            'type' => $this->getType(),
            'created' => $this->getCreated()
        ];
    }
}
