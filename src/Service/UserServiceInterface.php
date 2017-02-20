<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/13/2017
 * Time: 5:30 PM
 */

declare(strict_types = 1);

namespace Dot\User\Service;

use Dot\User\Entity\UserEntity;
use Dot\User\Result\Result;

/**
 * Interface UserServiceInterface
 * @package Dot\User\Service
 */
interface UserServiceInterface
{
    /**
     * @param $id
     * @param array $options
     * @return UserEntity|null
     */
    public function find($id, array $options = []): ?UserEntity;

    /**
     * @param UserEntity $user
     * @return mixed
     */
    public function delete(UserEntity $user);

    /**
     * @param UserEntity $user
     * @return Result
     */
    public function register(UserEntity $user): Result;

    /**
     * @param array $params
     * @return Result
     */
    public function confirmAccount(array $params): Result;

    /**
     * @param array $data
     * @return Result
     */
    public function resetPassword(array $data): Result;

    /**
     * @param UserEntity $user
     * @param array $data
     * @return Result
     */
    public function changePassword(UserEntity $user, array $data): Result;

    /**
     * @param UserEntity $user
     * @return Result
     */
    public function updateAccount(UserEntity $user): Result;

    /**
     * @param array $data
     * @return Result
     */
    public function resetPasswordRequest(array $data): Result;

    /**
     * @return UserEntity
     */
    public function newUser(): UserEntity;
}
