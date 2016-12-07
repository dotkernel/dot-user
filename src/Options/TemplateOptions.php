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

/**
 * Class TemplateOptions
 * @package Dot\User\Options
 */
class TemplateOptions extends AbstractOptions
{
    /** @var string */
    protected $loginTemplate = 'dot-user::login';

    protected $loginTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $registerTemplate = 'dot-user::register';

    /** @var string  */
    protected $registerTemplateLayout = '@layout/default.html.twig';

    /** @var string  */
    protected $accountTemplate = 'dot-user::account';

    /** @var string  */
    protected $accountTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $changePasswordTemplate = 'dot-user::change-password';

    /** @var string  */
    protected $changePasswordTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $forgotPasswordTemplate = 'dot-user::forgot-password';

    /** @var string  */
    protected $forgotPasswordTemplateLayout = '@layout/default.html.twig';

    /** @var string */
    protected $resetPasswordTemplate = 'dot-user::reset-password';

    /** @var string  */
    protected $resetPasswordTemplateLayout = '@layout/default.html.twig';

    protected $__strictMode__ = false;

    /**
     * @return string
     */
    public function getLoginTemplate()
    {
        return $this->loginTemplate;
    }

    /**
     * @param string $loginTemplate
     * @return TemplateOptions
     */
    public function setLoginTemplate($loginTemplate)
    {
        $this->loginTemplate = $loginTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegisterTemplate()
    {
        return $this->registerTemplate;
    }

    /**
     * @param string $registerTemplate
     * @return TemplateOptions
     */
    public function setRegisterTemplate($registerTemplate)
    {
        $this->registerTemplate = $registerTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getChangePasswordTemplate()
    {
        return $this->changePasswordTemplate;
    }

    /**
     * @param string $changePasswordTemplate
     * @return TemplateOptions
     */
    public function setChangePasswordTemplate($changePasswordTemplate)
    {
        $this->changePasswordTemplate = $changePasswordTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getForgotPasswordTemplate()
    {
        return $this->forgotPasswordTemplate;
    }

    /**
     * @param string $forgotPasswordTemplate
     * @return TemplateOptions
     */
    public function setForgotPasswordTemplate($forgotPasswordTemplate)
    {
        $this->forgotPasswordTemplate = $forgotPasswordTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getResetPasswordTemplate()
    {
        return $this->resetPasswordTemplate;
    }

    /**
     * @param string $resetPasswordTemplate
     * @return TemplateOptions
     */
    public function setResetPasswordTemplate($resetPasswordTemplate)
    {
        $this->resetPasswordTemplate = $resetPasswordTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountTemplate()
    {
        return $this->accountTemplate;
    }

    /**
     * @param string $accountTemplate
     * @return TemplateOptions
     */
    public function setAccountTemplate($accountTemplate)
    {
        $this->accountTemplate = $accountTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoginTemplateLayout()
    {
        return $this->loginTemplateLayout;
    }

    /**
     * @param string $loginTemplateLayout
     * @return TemplateOptions
     */
    public function setLoginTemplateLayout($loginTemplateLayout)
    {
        $this->loginTemplateLayout = $loginTemplateLayout;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegisterTemplateLayout()
    {
        return $this->registerTemplateLayout;
    }

    /**
     * @param string $registerTemplateLayout
     * @return TemplateOptions
     */
    public function setRegisterTemplateLayout($registerTemplateLayout)
    {
        $this->registerTemplateLayout = $registerTemplateLayout;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountTemplateLayout()
    {
        return $this->accountTemplateLayout;
    }

    /**
     * @param string $accountTemplateLayout
     * @return TemplateOptions
     */
    public function setAccountTemplateLayout($accountTemplateLayout)
    {
        $this->accountTemplateLayout = $accountTemplateLayout;
        return $this;
    }

    /**
     * @return string
     */
    public function getChangePasswordTemplateLayout()
    {
        return $this->changePasswordTemplateLayout;
    }

    /**
     * @param string $changePasswordTemplateLayout
     * @return TemplateOptions
     */
    public function setChangePasswordTemplateLayout($changePasswordTemplateLayout)
    {
        $this->changePasswordTemplateLayout = $changePasswordTemplateLayout;
        return $this;
    }

    /**
     * @return string
     */
    public function getForgotPasswordTemplateLayout()
    {
        return $this->forgotPasswordTemplateLayout;
    }

    /**
     * @param string $forgotPasswordTemplateLayout
     * @return TemplateOptions
     */
    public function setForgotPasswordTemplateLayout($forgotPasswordTemplateLayout)
    {
        $this->forgotPasswordTemplateLayout = $forgotPasswordTemplateLayout;
        return $this;
    }

    /**
     * @return string
     */
    public function getResetPasswordTemplateLayout()
    {
        return $this->resetPasswordTemplateLayout;
    }

    /**
     * @param string $resetPasswordTemplateLayout
     * @return TemplateOptions
     */
    public function setResetPasswordTemplateLayout($resetPasswordTemplateLayout)
    {
        $this->resetPasswordTemplateLayout = $resetPasswordTemplateLayout;
        return $this;
    }

}