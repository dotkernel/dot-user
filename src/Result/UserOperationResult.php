<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/22/2016
 * Time: 7:35 PM
 */

namespace Dot\User\Result;

use Dot\User\Entity\UserEntityInterface;

/**
 * Class UserOperationResult
 * @package Dot\User\Result
 */
class UserOperationResult extends AbstractResult
{
    /** @var  UserEntityInterface */
    protected $user;

    /**
     * @return UserEntityInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
