<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/13/2017
 * Time: 10:06 PM
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

use Dot\Ems\Entity\Entity;

/**
 * Class TokenEntity
 * @package Dot\User\Entity
 */
abstract class AbstractTokenEntity extends Entity
{
    const TOKEN_CONFIRM = 'confirm';
    const TOKEN_RESET = 'reset';
    const TOKEN_REMEMBER = 'remember';

    /** @var  mixed */
    protected $id;

    /** @var  string */
    protected $userId;

    /** @var  string */
    protected $token;

    /** @var  string */
    protected $expire;

    /** @var  string */
    protected $created;

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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getExpire(): string
    {
        return $this->expire;
    }

    /**
     * @param string $expire
     */
    public function setExpire(string $expire)
    {
        $this->expire = $expire;
    }

    /**
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    abstract public function getType(): string;
}
