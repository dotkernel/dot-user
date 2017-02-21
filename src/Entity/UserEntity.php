<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 2:52 PM
 */

namespace Dot\User\Entity;

use Dot\Authentication\Identity\IdentityInterface as AuthenticationIdentity;
use Dot\Authorization\Identity\IdentityInterface as AuthorizationIdentity;
use Dot\Ems\Entity\Entity;

/**
 * Class UserEntity
 * @package Dot\User\Entity
 */
class UserEntity extends Entity implements
    AuthenticationIdentity,
    AuthorizationIdentity
{
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_DELETED = 'deleted';

    /** @var  string */
    protected $id;

    /** @var  string */
    protected $username;

    /** @var  string */
    protected $email;

    /** @var  string */
    protected $password;

    /** @var array */
    protected $roles = [];

    /** @var string */
    protected $status = UserEntity::STATUS_PENDING;

    /** @var  string */
    protected $dateCreated;

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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
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
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles ?? [];
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
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
    public function setStatus(string $status)
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
    public function setDateCreated(string $dateCreated)
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
        return $this->email;
    }
}
