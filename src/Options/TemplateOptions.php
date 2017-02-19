<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/15/2017
 * Time: 7:05 PM
 */

declare(strict_types = 1);

namespace Dot\User\Options;

/**
 * Class TemplateOptions
 * @package Dot\User\Options
 */
class TemplateOptions
{
    /** @var string */
    protected $loginTemplate = 'dot-user::login';

    /** @var string */
    protected $loginTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $registerTemplate = 'dot-user::register';

    /** @var string */
    protected $registerTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $accountTemplate = 'dot-user::account';

    /** @var string */
    protected $accountTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $changePasswordTemplate = 'dot-user::change-password';

    /** @var string */
    protected $changePasswordTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $forgotPasswordTemplate = 'dot-user::forgot-password';

    /** @var string */
    protected $forgotPasswordTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $resetPasswordTemplate = 'dot-user::reset-password';

    /** @var string */
    protected $resetPasswordTemplateLayout = '@layout/default.html.twig';

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
    public function getLoginTemplateLayout(): string
    {
        return $this->loginTemplateLayout;
    }

    /**
     * @param string $loginTemplateLayout
     */
    public function setLoginTemplateLayout(string $loginTemplateLayout)
    {
        $this->loginTemplateLayout = $loginTemplateLayout;
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
    public function getRegisterTemplateLayout(): string
    {
        return $this->registerTemplateLayout;
    }

    /**
     * @param string $registerTemplateLayout
     */
    public function setRegisterTemplateLayout(string $registerTemplateLayout)
    {
        $this->registerTemplateLayout = $registerTemplateLayout;
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
    public function getAccountTemplateLayout(): string
    {
        return $this->accountTemplateLayout;
    }

    /**
     * @param string $accountTemplateLayout
     */
    public function setAccountTemplateLayout(string $accountTemplateLayout)
    {
        $this->accountTemplateLayout = $accountTemplateLayout;
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
    public function getChangePasswordTemplateLayout(): string
    {
        return $this->changePasswordTemplateLayout;
    }

    /**
     * @param string $changePasswordTemplateLayout
     */
    public function setChangePasswordTemplateLayout(string $changePasswordTemplateLayout)
    {
        $this->changePasswordTemplateLayout = $changePasswordTemplateLayout;
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
    public function getForgotPasswordTemplateLayout(): string
    {
        return $this->forgotPasswordTemplateLayout;
    }

    /**
     * @param string $forgotPasswordTemplateLayout
     */
    public function setForgotPasswordTemplateLayout(string $forgotPasswordTemplateLayout)
    {
        $this->forgotPasswordTemplateLayout = $forgotPasswordTemplateLayout;
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

    /**
     * @return string
     */
    public function getResetPasswordTemplateLayout(): string
    {
        return $this->resetPasswordTemplateLayout;
    }

    /**
     * @param string $resetPasswordTemplateLayout
     */
    public function setResetPasswordTemplateLayout(string $resetPasswordTemplateLayout)
    {
        $this->resetPasswordTemplateLayout = $resetPasswordTemplateLayout;
    }
}
