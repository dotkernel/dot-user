<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/24/2016
 * Time: 7:31 PM
 */

namespace Dot\User\Service;

use Dot\User\Options\UserOptions;

/**
 * Class PasswordDefault
 * @package Dot\User\Service
 */
class PasswordDefault implements PasswordInterface
{
    /** @var  UserOptions */
    protected $options;

    /**
     * PasswordDefault constructor.
     * @param UserOptions $options
     */
    public function __construct(UserOptions $options)
    {
        $this->options = $options;
    }

    /**
     * @param $hash
     * @param $password
     * @return bool
     */
    public function __invoke($hash, $password)
    {
        return $this->verify($hash, $password);
    }

    /**
     * @param $password
     * @return mixed
     */
    public function create($password)
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $this->options->getPasswordCost()]);
    }

    /**
     * @param $hash
     * @param $password
     * @return mixed
     */
    public function verify($hash, $password)
    {
        return password_verify($password, $hash);
    }

    /**
     * @param $hash
     * @return mixed
     */
    public function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT, ['cost' => $this->options->getPasswordCost()]);
    }

    /**
     * @param $hash
     * @return mixed
     */
    public function getInfo($hash)
    {
        return password_get_info($hash);
    }

}