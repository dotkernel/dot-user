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

    /** @var  string */
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
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId)
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
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated(string $created)
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    abstract public function getType(): string;
}
