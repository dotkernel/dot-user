<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 11/24/2016
 * Time: 8:01 PM
 */

namespace Dot\User\Form\InputFilter;


use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\InputFilter\InputFilter;
use Zend\Validator\AbstractValidator;

/**
 * Class UserInputFilter
 * @package Dot\User\Form\InputFilter
 */
class UserInputFilter extends InputFilter
{
    /** @var  UserOptions */
    protected $options;

    /** @var AbstractValidator */
    protected $emailValidator;

    /** @var AbstractValidator */
    protected $usernameValidator;

    /**
     * UserInputFilter constructor.
     * @param UserOptions $options
     * @param null $emailValidator
     * @param null $usernameValidator
     */
    public function __construct(
        UserOptions $options,
        $emailValidator = null,
        $usernameValidator = null
    ) {
        $this->options = $options;
        $this->emailValidator = $emailValidator;
        $this->usernameValidator = $usernameValidator;
    }

    public function init()
    {
        $this->add([
            'name' => 'id',
            'required' => false,
        ]);

        $email = [
            'name' => 'email',
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_EMPTY_EMAIL),
                    ]
                ],
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_INVALID_EMAIL)
                    ]
                ],
            ],
        ];

        if ($this->emailValidator) {
            $this->emailValidator->setMessage($this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REGISTER_EMAIL_ALREADY_REGISTERED));

            $email['validators'][] = $this->emailValidator;
        }

        $this->add($email);

        $username = [
            'name' => 'username',
            'filters' => [
                ['name' => 'StringTrim']
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_EMPTY_USERNAME)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 3,
                        'max' => 150,
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_USERNAME_CHARACTER_LIMIT)
                    ]
                ],
                [
                    'name' => 'Regex',
                    'options' => [
                        'pattern' => '/^[a-zA-Z0-9-_]+$/',
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS)
                    ]
                ],
            ],
        ];

        if ($this->usernameValidator) {
            $this->usernameValidator->setMessage($this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED));

            $username['validators'][] = $this->usernameValidator;
        }

        if ($this->options->getRegisterOptions()->isEnableUsername()) {
            $this->add($username);
        }

        $this->add([
            'name' => 'password',
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_EMPTY_PASSWORD)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'max' => 150,
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_PASSWORD_CHARACTER_LIMIT)
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'passwordVerify',
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_EMPTY_PASSWORD_CONFIRM)
                    ]
                ],
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'password',
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_REGISTER_PASSWORD_MISMATCH)
                    ],
                ],
            ],
        ]);
    }

}