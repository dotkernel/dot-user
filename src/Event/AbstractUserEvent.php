<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/20/2016
 * Time: 9:38 PM
 */

namespace Dot\User\Event;

use Dot\Event\Event;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Result\ResultInterface;
use Dot\User\Service\UserServiceInterface;

/**
 * Class AbstractUserEvent
 * @package Dot\User\Event
 */
abstract class AbstractUserEvent extends Event
{
    /** @var UserServiceInterface */
    protected $userService;

    /** @var UserEntityInterface */
    protected $user;

    /** @var ResultInterface|null */
    protected $result;

    /**
     * AbstractUserEvent constructor.
     * @param UserServiceInterface $userService
     * @param null|object|string $name
     * @param null|ResultInterface $result
     * @param UserEntityInterface|null $user
     */
    public function __construct(
        UserServiceInterface $userService,
        $name,
        UserEntityInterface $user = null,
        ResultInterface $result = null

    ) {
        $this->userService = $userService;
        $this->user = $user;
        $this->result = $result;
        parent::__construct($name);
    }

    /**
     * @return UserServiceInterface
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     * @param UserServiceInterface $userService
     * @return AbstractUserEvent
     */
    public function setUserService($userService)
    {
        $this->userService = $userService;
        return $this;
    }

    /**
     * @return UserEntityInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserEntityInterface $user
     * @return AbstractUserEvent
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return ResultInterface|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param ResultInterface|null $result
     * @return AbstractUserEvent
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
}
