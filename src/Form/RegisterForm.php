<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 2/5/2017
 * Time: 2:57 AM
 */

declare(strict_types = 1);

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Dot\User\Options\UserOptionsAwareTrait;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class RegisterForm
 * @package Dot\User\Form
 */
class RegisterForm extends Form implements UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

    public function __construct()
    {
        parent::__construct('registerForm');

        $this->setAttribute('method', 'post');
        $this->setInputFilter(new InputFilter());
    }

    public function init()
    {
        $this->add([
            'type' => 'UserFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ]
        ]);

        $this->add([
            'name' => 'captcha',
            'type' => 'Captcha',
            'options' => [
                'label' => 'Please verify you are human',
                'captcha' => [],
            ]
        ], ['priority' => -100]);

        $this->add([
            'type' => 'Csrf',
            'name' => 'register_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
                    'message' => $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::FORM_EXPIRED)
                ]
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Create account'
            ]
        ], ['priority' => -105]);

        $this->setValidationGroup([
            'register_csrf',
            'captcha',
            'user' => [
                'username',
                'email',
                'password',
                'passwordConfirm',
            ]
        ]);
    }
}
