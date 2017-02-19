<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 2/5/2017
 * Time: 3:45 AM
 */

declare(strict_types = 1);

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Dot\User\Options\UserOptionsAwareTrait;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class ChangePasswordForm
 * @package Dot\User\Form
 */
class ChangePasswordForm extends Form implements InputFilterProviderInterface, UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

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
