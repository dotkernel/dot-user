<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace Dot\User\Mapper;

use Dot\Mapper\MapperInterface;

/**
 * Interface UserMapperInterface
 * @package Dot\User\Mapper
 */
interface UserMapperInterface extends MapperInterface
{
    /**
     * Get a user entity object based on its id
     *
     * @param $id
     * @return mixed
     */
    public function findUser($id);

    /**
     * Get a user entity object based on a dynamically named field
     *
     * @param $field
     * @param $id
     * @return mixed
     */
    public function findUserBy($field, $id);

    /**
     * Get a list of user entity object, with optional filters
     *
     * @param array $filters
     * @return mixed
     */
    public function findAllUsers(array $filters = []);

    /**
     * Creates or updates a user, depending on the presence of the user id in the data
     *
     * @param $data
     * @return mixed
     */
    public function createUser($data);

    /**
     * @param $data
     * @return mixed
     */
    public function updateUser($data);

    /**
     * Removes a user or flags it as deleted
     *
     * @param $id
     * @return mixed
     */
    public function removeUser($id);

    /**
     * Insert a reset token into the backend
     *
     * @param $data
     * @return mixed
     */
    public function saveResetToken($data);

    /**
     * Creates a confirmation token in the backend
     *
     * @param $data
     * @return mixed
     */
    public function saveConfirmToken($data);

    /**
     * Gets a reset token for a user, if it exists
     *
     * @param $userId
     * @param $token
     * @return mixed
     */
    public function findResetToken($userId, $token);

    /**
     * Gets confirm token from the backend for a specific user
     *
     * @param $userId
     * @param $token
     * @return mixed
     */
    public function findConfirmToken($userId, $token);

    /**
     * Deletes or marks the confirm token as used and disabled
     *
     * @param $userId
     * @param $token
     * @return mixed
     */
    public function removeConfirmToken($userId, $token);

    /**
     * Creates a remember token in the backend
     *
     * @param $data
     * @return mixed
     */
    public function saveRememberToken($data);

    /**
     * Gets a remember token row from the backend based on its selector
     *
     * @param $selector
     * @return mixed
     */
    public function findRememberToken($selector);

    /**
     * Deletes remember tokens for a given user
     *
     * @param $userId
     * @return mixed
     */
    public function removeRememberToken($userId);
}