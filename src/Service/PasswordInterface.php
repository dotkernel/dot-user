<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/24/2016
 * Time: 7:25 PM
 */

namespace Dot\User\Service;

/**
 * Interface PasswordInterface
 * @package Dot\User\Service
 */
interface PasswordInterface
{
    /**
     * @param $password
     * @return mixed
     */
    public function create($password);

    /**
     * @param $hash
     * @param $password
     * @return bool
     */
    public function verify($hash, $password);

    /**
     * @param $hash
     * @return bool
     */
    public function needsRehash($hash);

    /**
     * @param $hash
     * @return mixed
     */
    public function getInfo($hash);
}