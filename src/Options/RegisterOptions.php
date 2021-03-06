<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Options;

use Dot\User\Entity\UserEntity;
use Laminas\Stdlib\AbstractOptions;

/**
 * Class RegisterOptions
 * @package Dot\User\Options
 */
class RegisterOptions extends AbstractOptions
{
    /** @var bool */
    protected $enableRegistration = true;

    /** @var string */
    protected $defaultUserStatus = UserEntity::STATUS_PENDING;

    /** @var bool */
    protected $useRegistrationCaptcha = true;

    /** @var array */
    protected $captchaOptions = [];

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

    /**
     * @return array
     */
    public function getCaptchaOptions(): array
    {
        return $this->captchaOptions;
    }

    /**
     * @param array $captchaOptions
     */
    public function setCaptchaOptions(array $captchaOptions)
    {
        $this->captchaOptions = $captchaOptions;
    }
}
