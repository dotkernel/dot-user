<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/15/2017
 * Time: 3:07 PM
 */

declare(strict_types = 1);

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class RegisterOptions
 * @package Dot\User\Options
 */
class RegisterOptions extends AbstractOptions
{
    /** @var bool */
    protected $enableRegistration = false;

    /** @var string */
    protected $defaultUserStatus = 'pending';

    /** @var bool */
    protected $useRegistrationCaptcha = true;

    /** @var bool */
    protected $loginAfterRegistration = false;

    /**
     * @return bool
     */
    public function isEnableRegistration(): bool
    {
        return $this->enableRegistration;
    }

    /**
     * @param bool $enableRegistration
     */
    public function setEnableRegistration(bool $enableRegistration)
    {
        $this->enableRegistration = $enableRegistration;
    }

    /**
     * @return string
     */
    public function getDefaultUserStatus(): string
    {
        return $this->defaultUserStatus;
    }

    /**
     * @param string $defaultUserStatus
     */
    public function setDefaultUserStatus(string $defaultUserStatus)
    {
        $this->defaultUserStatus = $defaultUserStatus;
    }

    /**
     * @return bool
     */
    public function isUseRegistrationCaptcha(): bool
    {
        return $this->useRegistrationCaptcha;
    }

    /**
     * @param bool $useRegistrationCaptcha
     */
    public function setUseRegistrationCaptcha(bool $useRegistrationCaptcha)
    {
        $this->useRegistrationCaptcha = $useRegistrationCaptcha;
    }

    /**
     * @return bool
     */
    public function isLoginAfterRegistration(): bool
    {
        return $this->loginAfterRegistration;
    }

    /**
     * @param bool $loginAfterRegistration
     */
    public function setLoginAfterRegistration(bool $loginAfterRegistration)
    {
        $this->loginAfterRegistration = $loginAfterRegistration;
    }
}
