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
