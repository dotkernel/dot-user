<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

/**
 * Class RememberTokenEntity
 * @package Dot\User\Entity
 */
class RememberTokenEntity extends AbstractSelectorTokenEntity implements \JsonSerializable
{
    public function getType(): string
    {
        return AbstractTokenEntity::TOKEN_REMEMBER;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'userId' => $this->getUserId(),
            'selector' => $this->getSelector(),
            'token' => $this->getToken(),
            'expire' => $this->getExpire(),
            'type' => $this->getType(),
            'created' => $this->getCreated()
        ];
    }
}
