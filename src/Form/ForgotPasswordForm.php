<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 2/5/2017
 * Time: 3:56 AM
 */

declare(strict_types = 1);

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Dot\User\Options\UserOptionsAwareTrait;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class ForgotPasswordForm
 * @package Dot\User\Form
 */
class ForgotPasswordForm extends Form implements InputFilterProviderInterface, UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

    /**
     * ForgotPasswordForm constructor.
     */
    public function __construct()
    {
        parent::__construct('forgotPasswordForm');
        $this->setAttribute('method', 'post');
    }

    public function init()
    {
        $this->add([
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'Email associated with your account',
            ],
            'attributes' => [
                'placeholder' => 'Your e-mail address...',
                //'required' => 'required',
            ]
        ]);

        $this->add([
            'name' => 'forgot_password_csrf',
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
                'value' => 'Reset password'
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'email' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::EMAIL_EMPTY)
                        ]
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::EMAIL_INVALID)
                        ]
                    ]
                ]
            ]
        ];
    }
}
