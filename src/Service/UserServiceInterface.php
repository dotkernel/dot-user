<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/5/2016
 * Time: 11:08 PM
 */

namespace Dot\User\Service;

use Dot\User\Entity\UserEntityInterface;
use Dot\User\Mapper\UserMapperInterface;
use Dot\User\Options\UserOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface UserServiceInterface
 * @package Dot\User\Service
 */
interface UserServiceInterface
{
    /**
     * Find user by its id
     *
     * @param $id
     * @return mixed
     */
    public function findUser($id);

    /**
     * Get a user entity by some given field and value
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findUserBy($field, $value);

    /**
     * Save user is working as in create/update user, based on the presence of user id in the data
     *
     * @param UserEntityInterface $user
     * @return mixed
     */
    public function saveUser(UserEntityInterface $user);

    /**
     * Remove an user based on its id
     *
     * @param $id
     * @return mixed
     */
    public function removeUser($id);

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
     * @param UserEntityInterface $user
     * @return bool|UserEntityInterface
     */
    public function register(UserEntityInterface $user);

    /**
     * @param UserEntityInterface $user
     * @return mixed
     */
    public function generateRememberToken(UserEntityInterface $user);

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
     * @param UserEntityInterface $user
     * @return mixed
     */
    public function removeRememberToken(UserEntityInterface $user);

    /**
     * @return UserEntityInterface
     */
    public function getUserEntityPrototype();

    /**
     * @param UserEntityInterface $userEntityPrototype
     * @return UserService
     */
    public function setUserEntityPrototype($userEntityPrototype);

    /**
     * @return mixed
     */
    //public function getOptions();

    /**
     * @param UserOptions $options
     * @return mixed
     */
    //public function setOptions(UserOptions $options);

    /**
     * @param UserMapperInterface $userMapper
     * @return mixed
     */
    //public function setUserMapper(UserMapperInterface $userMapper);

    /**
     * @return UserMapperInterface
     */
    //public function getUserMapper();

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function setRequest(ServerRequestInterface $request);

    /**
     * @return ServerRequestInterface
     */
    //public function getRequest();

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function setResponse(ResponseInterface $response);

    /**
     * @return ResponseInterface
     */
    //public function getResponse();
}