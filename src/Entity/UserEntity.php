<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace Dot\User\Entity;

use Dot\Authentication\Identity\IdentityInterface as AuthenticationIdentityInterface;
use Dot\Authorization\Identity\IdentityInterface as AuthorizationIdentityInterface;

class UserEntity implements
    UserEntityInterface,
    AuthenticationIdentityInterface,
    AuthorizationIdentityInterface
{
    /** @var  string|int */
    protected $id;

    /** @var  string */
    protected $username;

    /** @var  string */
    protected $email;

    /** @var  string */
    protected $password;

    /** @var  string */
    protected $role;

    /** @var  string */
    protected $status;

    /** @var  string|int */
    protected $dateCreated;

    /**
     * @return string
     */
    public function getName()
    {
        if($this->username) {
            return $this->username;
        }

        return $this->email;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     * @return UserEntity
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return UserEntity
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
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
     * @return UserEntity
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
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
     * @return UserEntity
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param int|string $dateCreated
     * @return UserEntity
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
        return $this;
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
     * @return UserEntity
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getRoles()
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return UserEntity
     */
    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    
}