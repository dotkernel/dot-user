<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/23/2016
 * Time: 3:44 PM
 */

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class LoginOptions
 * @package Dot\User\Options
 */
class LoginOptions extends AbstractOptions
{
    /** @var bool  */
    protected $enableRememberMe = true;

    /** @var string  */
    protected $rememberMeCookieName = 'rememberMe';

    /** @var int  */
    protected $rememberMeCookieExpire = 60*60*24*30;

    /** @var bool  */
    protected $rememberMeCookieSecure = false;

    /** @var array  */
    protected $authIdentityFields = ['username', 'email'];

    /** @var  array */
    protected $allowedLoginStatuses = ['active'];

    /** @var int  */
    protected $loginFormTimeout = 1800;

    /**
     * @return boolean
     */
    public function isEnableRememberMe()
    {
        return $this->enableRememberMe;
    }

    /**
     * @param boolean $enableRememberMe
     * @return LoginOptions
     */
    public function setEnableRememberMe($enableRememberMe)
    {
        $this->enableRememberMe = $enableRememberMe;
        return $this;
    }

    /**
     * @return array
     */
    public function getAuthIdentityFields()
    {
        return $this->authIdentityFields;
    }

    /**
     * @param array $authIdentityFields
     * @return LoginOptions
     */
    public function setAuthIdentityFields($authIdentityFields)
    {
        $this->authIdentityFields = (array) $authIdentityFields;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllowedLoginStatuses()
    {
        return $this->allowedLoginStatuses;
    }

    /**
     * @param array $allowedLoginStatuses
     * @return LoginOptions
     */
    public function setAllowedLoginStatuses($allowedLoginStatuses)
    {
        $this->allowedLoginStatuses = $allowedLoginStatuses;
        return $this;
    }

    /**
     * @return int
     */
    public function getLoginFormTimeout()
    {
        return $this->loginFormTimeout;
    }

    /**
     * @param int $loginFormTimeout
     * @return LoginOptions
     */
    public function setLoginFormTimeout($loginFormTimeout)
    {
        $this->loginFormTimeout = $loginFormTimeout;
        return $this;
    }

    /**
     * @return string
     */
    public function getRememberMeCookieName()
    {
        return $this->rememberMeCookieName;
    }

    /**
     * @param string $rememberMeCookieName
     * @return LoginOptions
     */
    public function setRememberMeCookieName($rememberMeCookieName)
    {
        $this->rememberMeCookieName = $rememberMeCookieName;
        return $this;
    }

    /**
     * @return int
     */
    public function getRememberMeCookieExpire()
    {
        return $this->rememberMeCookieExpire;
    }

    /**
     * @param int $rememberMeCookieExpire
     * @return LoginOptions
     */
    public function setRememberMeCookieExpire($rememberMeCookieExpire)
    {
        $this->rememberMeCookieExpire = $rememberMeCookieExpire;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isRememberMeCookieSecure()
    {
        return $this->rememberMeCookieSecure;
    }

    /**
     * @param boolean $rememberMeCookieSecure
     * @return LoginOptions
     */
    public function setRememberMeCookieSecure($rememberMeCookieSecure)
    {
        $this->rememberMeCookieSecure = $rememberMeCookieSecure;
        return $this;
    }


}