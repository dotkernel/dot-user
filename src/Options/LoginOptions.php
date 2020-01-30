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
 * Class LoginOptions
 * @package Dot\User\Options
 */
class LoginOptions extends AbstractOptions
{
    /** @var bool */
    protected $enableRemember = true;

    /** @var string */
    protected $rememberCookieName = 'remember';

    /** @var bool */
    protected $rememberCookieSecure = false;

    /** @var int */
    protected $rememberTokenExpire = 3600 * 24 * 30;

    /** @var array */
    protected $allowedStatus = [UserEntity::STATUS_ACTIVE];

    /**
     * @return bool
     */
    public function isEnableRemember(): bool
    {
        return $this->enableRemember;
    }

    /**
     * @param bool $enableRemember
     */
    public function setEnableRemember(bool $enableRemember)
    {
        $this->enableRemember = $enableRemember;
    }

    /**
     * @return string
     */
    public function getRememberCookieName(): string
    {
        return $this->rememberCookieName;
    }

    /**
     * @param string $rememberCookieName
     */
    public function setRememberCookieName(string $rememberCookieName)
    {
        $this->rememberCookieName = $rememberCookieName;
    }

    /**
     * @return int
     */
    public function getRememberTokenExpire(): int
    {
        return $this->rememberTokenExpire;
    }

    /**
     * @param int $rememberTokenExpire
     */
    public function setRememberTokenExpire(int $rememberTokenExpire)
    {
        $this->rememberTokenExpire = $rememberTokenExpire;
    }

    /**
     * @return bool
     */
    public function isRememberCookieSecure(): bool
    {
        return $this->rememberCookieSecure;
    }

    /**
     * @param bool $rememberCookieSecure
     */
    public function setRememberCookieSecure(bool $rememberCookieSecure)
    {
        $this->rememberCookieSecure = $rememberCookieSecure;
    }

    /**
     * @return array
     */
    public function getAllowedStatus(): array
    {
        return $this->allowedStatus;
    }

    /**
     * @param array $allowedStatus
     */
    public function setAllowedStatus(array $allowedStatus)
    {
        $this->allowedStatus = $allowedStatus;
    }
}
