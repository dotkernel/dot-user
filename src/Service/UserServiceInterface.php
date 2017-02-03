<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/5/2016
 * Time: 11:08 PM
 */

declare(strict_types=1);

namespace Dot\User\Service;

use Dot\Ems\Service\ServiceInterface;
use Dot\User\Entity\UserEntity;

/**
 * Interface UserServiceInterface
 * @package Dot\User\Service
 */
interface UserServiceInterface extends ServiceInterface
{
    /**
     * Change user status from unconfirmed to active based on an email and valid confirmation token
     *
     * @param $email
     * @param $token
     * @return array
     */
    public function confirmAccount($email, $token);

    /**
     * Based on a user email, generate a token and store a hash of it with and expiration time
     * trigger a specific event, so mail service can send an email based on it
     *
     * @param $data
     * @return bool
     */
    public function generateResetToken($data);

    /**
     * @param $email
     * @param $token
     * @param $data
     * @return array
     */
    public function resetPassword($email, $token, $data);

    /**
     * @param $oldPassword
     * @param $newPassword
     * @return mixed
     */
    public function changePassword($oldPassword, $newPassword);

    /**
     * Store a new user into the db, after it validates the data
     * trigger register events
     *
     * @param UserEntity $user
     * @return bool|UserEntity
     */
    public function register(UserEntity $user);

    /**
     * @param UserEntity $user
     * @return mixed
     */
    public function updateAccount(UserEntity $user);

    /**
     * @param UserEntity $user
     * @return mixed
     */
    public function generateRememberToken(UserEntity $user);

    /**
     * Validates the remember me cookie data
     *
     * @param $selector
     * @param $token
     * @return mixed
     */
    public function checkRememberToken($selector, $token);

    /**
     * Removes all remember tokens for a given user
     *
     * @param UserEntity $user
     * @return mixed
     */
    public function removeRememberToken(UserEntity $user);
}
