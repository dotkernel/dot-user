<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class TemplateOptions
 * @package Dot\User\Options
 */
class TemplateOptions extends AbstractOptions
{
    /** @var string */
    protected $loginTemplate = '';

    /** @var string */
    protected $registerTemplate = '';

    /** @var string */
    protected $accountTemplate = '';

    /** @var string */
    protected $changePasswordTemplate = '';

    /** @var string */
    protected $forgotPasswordTemplate = '';

    /** @var string */
    protected $resetPasswordTemplate = '';

    /**
     * @return string
     */
    public function getLoginTemplate(): string
    {
        return $this->loginTemplate;
    }

    /**
     * @param string $loginTemplate
     */
    public function setLoginTemplate(string $loginTemplate)
    {
        $this->loginTemplate = $loginTemplate;
    }

    /**
     * @return string
     */
    public function getRegisterTemplate(): string
    {
        return $this->registerTemplate;
    }

    /**
     * @param string $registerTemplate
     */
    public function setRegisterTemplate(string $registerTemplate)
    {
        $this->registerTemplate = $registerTemplate;
    }

    /**
     * @return string
     */
    public function getAccountTemplate(): string
    {
        return $this->accountTemplate;
    }

    /**
     * @param string $accountTemplate
     */
    public function setAccountTemplate(string $accountTemplate)
    {
        $this->accountTemplate = $accountTemplate;
    }

    /**
     * @return string
     */
    public function getChangePasswordTemplate(): string
    {
        return $this->changePasswordTemplate;
    }

    /**
     * @param string $changePasswordTemplate
     */
    public function setChangePasswordTemplate(string $changePasswordTemplate)
    {
        $this->changePasswordTemplate = $changePasswordTemplate;
    }

    /**
     * @return string
     */
    public function getForgotPasswordTemplate(): string
    {
        return $this->forgotPasswordTemplate;
    }

    /**
     * @param string $forgotPasswordTemplate
     */
    public function setForgotPasswordTemplate(string $forgotPasswordTemplate)
    {
        $this->forgotPasswordTemplate = $forgotPasswordTemplate;
    }

    /**
     * @return string
     */
    public function getResetPasswordTemplate(): string
    {
        return $this->resetPasswordTemplate;
    }

    /**
     * @param string $resetPasswordTemplate
     */
    public function setResetPasswordTemplate(string $resetPasswordTemplate)
    {
        $this->resetPasswordTemplate = $resetPasswordTemplate;
    }
}
