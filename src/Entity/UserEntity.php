<?php
/**
 * see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

namespace Dot\User\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dot\Authentication\Identity\IdentityInterface as AuthenticationIdentity;
use Dot\Authorization\Identity\IdentityInterface as AuthorizationIdentity;
use Doctrine\ORM\Mapping as ORM;
// use Dot\Mapper\Entity\Entity;
use Dot\User\Entity\UserEntityRepository;

/**
 * Class UserEntity
 * @package Dot\User\Entity
 * @ORM\Table("user")
 * @ORM\Entity(repositoryClass="UserEntityRepository")
 */
class UserEntity /* extends Entity*/ implements
    AuthenticationIdentity,
    AuthorizationIdentity,
    \JsonSerializable
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $username;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime")
     * @var string
     */
    protected $dateCreated;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="RoleEntity", inversedBy="user", fetch="EXTRA_LAZY")
     * @ORM\JoinTable(name="user_roles",
     *            joinColumns={@ORM\JoinColumn(name="userId", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="roleId", referencedColumnName="id")}
     *   )
     * @var ArrayCollection
     */
    private $roles;

    /**
     * @return mixed
     */
    public function getRoles(): array
    {
        return $this->roles->toArray() ?? [];
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword():? string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getDateCreated(): string
    {
        return $this->dateCreated;
    }

    /**
     * @param string $dateCreated
     */
    public function setDateCreated(string $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if ($this->username) {
            return $this->username;
        }
        return $this->email ?? '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername();
    }


    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'roles' => $this->getRoles(),
            'status' => $this->getStatus(),
            'dateCreated' => $this->getDateCreated()
        ];
    }
}