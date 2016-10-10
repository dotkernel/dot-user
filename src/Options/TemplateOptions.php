<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 10/9/2016
 * Time: 11:51 PM
 */

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class TemplateOptions
 * @package Dot\User\Options
 */
class TemplateOptions extends AbstractOptions
{
    /** @var string  */
    protected $loginTemplate = 'dot-user::login';

    /** @var string  */
    protected $registerTemplate = 'dot-user::register';

    /** @var string  */
    protected $changePasswordTemplate = 'dot-user::change-password';

    /** @var string  */
    protected $forgotPasswordTemplate = 'dot-user::forgot-password';

    /** @var string  */
    protected $resetPasswordTemplate = 'dot-user::reset-password';

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

}