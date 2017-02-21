<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/14/2017
 * Time: 12:01 AM
 */

declare(strict_types = 1);

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class LoginOptions
 * @package Dot\User\Options
 */
class LoginOptions extends AbstractOptions
{
    /** @var bool */
    protected $enableRemember = true;

    /** @var string */
    protected $rememberCookieName = 'rememberMe';

    /** @var int */
    protected $rememberCookieExpire = 3600 * 24 * 30;

    /** @var bool */
    protected $rememberCookieSecure = false;

    /** @var array */
    protected $allowedStatus = ['active'];

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
    public function getRememberCookieExpire(): int
    {
        return $this->rememberCookieExpire;
    }

    /**
     * @param int $rememberCookieExpire
     */
    public function setRememberCookieExpire(int $rememberCookieExpire)
    {
        $this->rememberCookieExpire = $rememberCookieExpire;
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
