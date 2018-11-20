<?php
/**
 * @ORM\see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @ORM\copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @ORM\license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dot\Mapper\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RoleEntity
 * @package Dot\User\Entity
 * @ORM\Entity()
 * @ORM\Table("role")
 */
class RoleEntity extends Entity implements \JsonSerializable
{
    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue *
     */
    protected $id;

    /** @var string @ORM\Column(type="string") **/
    protected $name;

    /**
     * Many Groups have Many Users.
     * @ORM\OneToMany(targetEntity="UserEntity", mappedBy="roles")
     * @ORM\JoinTable(name="user_roles",
     *            joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     *   )
     */
    private $users;

    /**
     * @return mixed
     */
    public function getUsers() : array
    {
        return $this->users->toArray() ?? [];
    }

    public function __construct() {
        $this->users = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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