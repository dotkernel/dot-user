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
 * Class ChangePasswordForm
 * @package Dot\User\Form
 */
class ChangePasswordForm extends Form implements InputFilterProviderInterface, UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

    /**
     * ChangePasswordForm constructor.
     */
    public function __construct()
    {
        parent::__construct('changePasswordForm');
        $this->setAttribute('method', 'post');
    }

    public function init()
    {
        $this->add([
            'name' => 'currentPassword',
            'type' => 'Password',
            'options' => [
                'label' => 'Your current password',
            ],
            'attributes' => [
                'placeholder' => 'Current password...',
                //'required' => 'required',
            ]
        ]);

        $this->add([
            'type' => 'UserFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ]
        ]);

        $this->add([
            'name' => 'change_password_csrf',
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
            'type' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Change password'
            ]
        ]);

        $this->getBaseFieldset()->get('password')
            ->setLabel('New password')
            ->setAttribute('placeholder', 'New password...');

        $this->getBaseFieldset()->get('passwordConfirm')
            ->setLabel('Confirm new password')
            ->setAttribute('placeholder', 'New password confirm...');

        $this->setValidationGroup([
            'change_password_csrf',
            'currentPassword',
            'user' => [
                'password',
                'passwordConfirm'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'currentPassword' => [
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::CURRENT_PASSWORD_EMPTY)
                        ]
                    ]
                ]
            ]
        ];
    }
}
