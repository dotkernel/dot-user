<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/3/2017
 * Time: 8:24 PM
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

/**
 * Class ConfirmTokenEntity
 * @package Dot\User\Entity
 */
class ConfirmTokenEntity extends AbstractTokenEntity implements \JsonSerializable
{
    public function getType(): string
    {
        return AbstractTokenEntity::TOKEN_CONFIRM;
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
            'type' => $this->getType(),
            'created' => $this->getCreated()
        ];
    }
}
