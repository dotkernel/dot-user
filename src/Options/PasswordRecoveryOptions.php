<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/6/2016
 * Time: 8:13 PM
 */

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class PasswordRecoveryOptions
 * @package Dot\User\Options
 */
class PasswordRecoveryOptions extends AbstractOptions
{
    /** @var bool  */
    protected $enablePasswordRecovery = true;

    /** @var int  */
    protected $resetPasswordTokenTimeout = 1800;

    /**
     * @return boolean
     */
    public function isEnablePasswordRecovery()
    {
        return $this->enablePasswordRecovery;
    }

    /**
     * @param boolean $enablePasswordRecovery
     * @return PasswordRecoveryOptions
     */
    public function setEnablePasswordRecovery($enablePasswordRecovery)
    {
        $this->enablePasswordRecovery = $enablePasswordRecovery;
        return $this;
    }

    /**
     * @return int
     */
    public function getResetPasswordTokenTimeout()
    {
        return $this->resetPasswordTokenTimeout;
    }

    /**
     * @param int $resetPasswordTokenTimeout
     * @return PasswordRecoveryOptions
     */
    public function setResetPasswordTokenTimeout($resetPasswordTokenTimeout)
    {
        $this->resetPasswordTokenTimeout = $resetPasswordTokenTimeout;
        return $this;
    }

}