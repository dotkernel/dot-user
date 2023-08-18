<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

namespace Dot\User\Entity;

use Dot\Authentication\Identity\IdentityInterface as AuthenticationIdentity;
use Dot\Authorization\Identity\IdentityInterface as AuthorizationIdentity;
use Dot\Mapper\Entity\Entity;

/**
 * Class UserEntity
 * @package Dot\User\Entity
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

    /** @var  mixed */
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

    public function getId(): int
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
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated)
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
