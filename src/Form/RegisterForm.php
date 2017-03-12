<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
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

    /**
     * RegisterForm constructor.
     */
    public function __construct()
    {
        parent::__construct('registerForm');

        $this->setAttribute('method', 'post');
        $this->setInputFilter(new InputFilter());
    }

    public function init()
    {
        $validationGroup = [
            'register_csrf',
            'user' => [
                'username',
                'email',
                'password',
                'passwordConfirm',
            ]
        ];

        $this->add([
            'type' => 'UserFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ]
        ]);

        if ($this->userOptions->getRegisterOptions()->isUseRegistrationCaptcha()
            && !empty($this->userOptions->getRegisterOptions()->getCaptchaOptions())
        ) {
            $this->add([
                'name' => 'captcha',
                'type' => 'Captcha',
                'options' => [
                    'label' => 'Please verify you are human',
                    'captcha' => $this->userOptions->getRegisterOptions()->getCaptchaOptions(),
                ]
            ], ['priority' => -100]);

            array_push($validationGroup, 'captcha');
        }

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
            'type' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Create account'
            ]
        ], ['priority' => -105]);

        $this->setValidationGroup($validationGroup);
    }
}
