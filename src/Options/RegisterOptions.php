<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/23/2016
 * Time: 7:48 PM
 */

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

class RegisterOptions extends AbstractOptions
{
    /** @var bool  */
    protected $enableRegistration = true;

    /** @var bool  */
    protected $enableUsername = true;

    /** @var  mixed */
    protected $defaultUserStatus = 'pending';

    /** @var int  */
    protected $userFormTimeout = 1800;

    /** @var bool  */
    protected $useRegistrationFormCaptcha = true;

    /** @var  mixed */
    protected $formCaptchaOptions = [
        'class'   => 'Figlet',
        'options' => [
            'wordLen'    => 5,
            'expiration' => 300,
            'timeout'    => 300,
        ],
    ];

    /** @var bool  */
    protected $loginAfterRegistration = false;

    /**
     * @return boolean
     */
    public function isEnableRegistration()
    {
        return $this->enableRegistration;
    }

    /**
     * @param boolean $enableRegistration
     * @return RegisterOptions
     */
    public function setEnableRegistration($enableRegistration)
    {
        $this->enableRegistration = $enableRegistration;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnableUsername()
    {
        return $this->enableUsername;
    }

    /**
     * @param boolean $enableUsername
     * @return RegisterOptions
     */
    public function setEnableUsername($enableUsername)
    {
        $this->enableUsername = $enableUsername;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultUserStatus()
    {
        return $this->defaultUserStatus;
    }

    /**
     * @param mixed $defaultUserStatus
     * @return RegisterOptions
     */
    public function setDefaultUserStatus($defaultUserStatus)
    {
        $this->defaultUserStatus = $defaultUserStatus;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserFormTimeout()
    {
        return $this->userFormTimeout;
    }

    /**
     * @param int $userFormTimeout
     * @return RegisterOptions
     */
    public function setUserFormTimeout($userFormTimeout)
    {
        $this->userFormTimeout = $userFormTimeout;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUseRegistrationFormCaptcha()
    {
        return $this->useRegistrationFormCaptcha;
    }

    /**
     * @param boolean $useRegistrationFormCaptcha
     * @return RegisterOptions
     */
    public function setUseRegistrationFormCaptcha($useRegistrationFormCaptcha)
    {
        $this->useRegistrationFormCaptcha = $useRegistrationFormCaptcha;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormCaptchaOptions()
    {
        return $this->formCaptchaOptions;
    }

    /**
     * @param mixed $formCaptchaOptions
     * @return RegisterOptions
     */
    public function setFormCaptchaOptions($formCaptchaOptions)
    {
        $this->formCaptchaOptions = $formCaptchaOptions;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isLoginAfterRegistration()
    {
        return $this->loginAfterRegistration;
    }

    /**
     * @param boolean $loginAfterRegistration
     * @return RegisterOptions
     */
    public function setLoginAfterRegistration($loginAfterRegistration)
    {
        $this->loginAfterRegistration = $loginAfterRegistration;
        return $this;
    }
    
}