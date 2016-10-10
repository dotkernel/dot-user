<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/21/2016
 * Time: 7:53 PM
 */

namespace Dot\User\Form;

use Dot\User\Factory\Form\ChangePasswordFormFactory;
use Dot\User\Factory\Form\ForgotPasswordFormFactory;
use Dot\User\Factory\Form\LoginFormFactory;
use Dot\User\Factory\Form\RegisterFormFactory;
use Dot\User\Factory\Form\ResetPasswordFormFactory;
use Zend\Form\Form;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Class FormManager
 * @package Dot\User\Form
 */
class UserFormManager extends AbstractPluginManager
{
    protected $instanceOf = Form::class;

    protected $factories = [
        LoginForm::class => LoginFormFactory::class,
        RegisterForm::class => RegisterFormFactory::class,
        ResetPasswordForm::class => ResetPasswordFormFactory::class,
        ForgotPasswordForm::class => ForgotPasswordFormFactory::class,
        ChangePasswordForm::class => ChangePasswordFormFactory::class,
    ];
}