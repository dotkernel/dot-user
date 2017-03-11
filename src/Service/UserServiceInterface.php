<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
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
     * @param string $email
     * @param array $options
     * @return UserEntity|null
     */
    public function findByEmail(string $email, array $options = []): ?UserEntity;

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
    public function optOut(array $params): Result;

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
     * @param bool $hashPassword
     * @return Result
     */
    public function updateAccount(UserEntity $user, bool $hashPassword = false): Result;

    /**
     * @param array $data
     * @return Result
     */
    public function resetPasswordRequest(array $data): Result;

    /**
     * @return UserEntity
     */
    public function newUser(): UserEntity;

    /**
     * @return TokenServiceInterface
     */
    public function getTokenService(): TokenServiceInterface;
}
