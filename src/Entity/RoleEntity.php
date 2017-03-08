<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/3/2017
 * Time: 8:13 PM
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

use Dot\Mapper\Entity\Entity;

/**
 * Class RoleEntity
 * @package Dot\User\Entity
 */
class RoleEntity extends Entity implements \JsonSerializable
{
    /** @var  mixed */
    protected $id;

    /** @var  string */
    protected $name;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
