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

/**
 * Class UserEntity
 * @package Dot\User\Entity
 * @ORM\Table("user")
 * @ORM\Entity
 */
class UserEntity extends Entity implements
    AuthenticationIdentity,
    AuthorizationIdentity,
    \JsonSerializable
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    /** @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue *
     */
    protected $id;

    /** @var string @ORM\Column(type="string") **/
    protected $email;

    /** @var string @ORM\Column(type="string") **/
    protected $username;

    /** @var string @ORM\Column(type="string") **/
    protected $password;

    /** @var string @ORM\Column(type="string") **/
    protected $status;

    /** @var DateTime @ORM\Column(type="datetime") **/
    protected $dateCreated;

    /**
     * Many Users have Many Groups.
     * @ORM\OneToMany(targetEntity="RoleEntity", inversedBy="user")
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
     * @ORM\param mixed $id
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
     * @ORM\param string $email
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
     * @ORM\param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @ORM\param string $password
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
     * @ORM\param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @ORM\param DateTime $dateCreated
     */
    public function setDateCreated(DateTime $dateCreated): void
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername();
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