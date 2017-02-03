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
use Dot\User\Entity\UserEntity;
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

    /** @var UserEntity */
    protected $user;

    /** @var ResultInterface|null */
    protected $result;

    /**
     * AbstractUserEvent constructor.
     * @param UserServiceInterface $userService
     * @param string $name
     * @param null|ResultInterface $result
     * @param UserEntity|null $user
     */
    public function __construct(
        UserServiceInterface $userService,
        string $name,
        UserEntity $user = null,
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
    public function getUserService(): UserServiceInterface
    {
        return $this->userService;
    }

    /**
     * @param UserServiceInterface $userService
     */
    public function setUserService(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     */
    public function setUser(UserEntity $user)
    {
        $this->user = $user;
    }

    /**
     * @return ResultInterface|null
     */
    public function getResult(): ?ResultInterface
    {
        return $this->result;
    }

    /**
     * @param ResultInterface|null $result
     */
    public function setResult(ResultInterface $result)
    {
        $this->result = $result;
    }
}
