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

use Dot\Ems\Entity\Entity;

/**
 * Class RoleEntity
 * @package Dot\User\Entity
 */
class RoleEntity extends Entity
{
    /** @var  string */
    protected $id;

    /** @var  string */
    protected $name;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
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
}