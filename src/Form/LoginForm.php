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
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;

/**
 * Class LoginForm
 * @package Dot\User\Form
 */
class LoginForm extends Form implements InputFilterProviderInterface, UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

    /**
     * LoginForm constructor.
     */
    public function __construct()
    {
        parent::__construct('loginForm');
        $this->setAttribute('method', 'post');
    }

    public function init()
    {
        $this->add([
            'name' => 'identity',
            'type' => 'text',
            'options' => [
                'label' => 'Username or email',
            ],
            'attributes' => [
                //'required' => 'required',
                'placeholder' => 'Username or email...',
                'autofocus' => true,
            ]
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'Password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                //'required' => 'required',
                'placeholder' => 'Password...',
            ]
        ], ['priority' => -10]);

        if ($this->userOptions->getLoginOptions()->isEnableRemember()) {
            $this->add(array(
                'type' => 'checkbox',
                'name' => 'remember',
                'options' => [
                    'label' => 'Remember Me',
                    'use_hidden_element' => true,
                    'checked_value' => 'yes',
                    'unchecked_value' => 'no',
                ],
                'attributes' => [
                    'value' => 'yes'
                ],
            ), ['priority' => -100]);
        }

        $this->add([
            'name' => 'login_csrf',
            'type' => 'Csrf',
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
            'type' => 'Submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Sign In'
            ]
        ], ['priority' => -105]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'identity' => [
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::IDENTITY_EMPTY)
                        ]
                    ]
                ]
            ],
            'password' => [
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::PASSWORD_EMPTY)
                        ]
                    ]
                ]
            ]
        ];
    }
}
