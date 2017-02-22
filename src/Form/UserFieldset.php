<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/3/2017
 * Time: 8:33 PM
 */

declare(strict_types = 1);

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Dot\User\Options\UserOptionsAwareTrait;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Class UserFieldset
 * @package Dot\User\Form
 */
class UserFieldset extends Fieldset implements InputFilterProviderInterface, UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

    /**
     * UserFieldset constructor.
     */
    public function __construct()
    {
        parent::__construct('user');
    }

    public function init()
    {
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
            'attributes' => [
                'id' => 'username',
                'placeholder' => 'Username...',
                //'required' => 'required',
            ]
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'placeholder' => 'Email...',
                //'required' => 'required',
            ]
        ], ['priority' => -5]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'placeholder' => 'Password...',
                //'required' => 'required',
            ]
        ], ['priority' => -20]);

        $this->add([
            'name' => 'passwordConfirm',
            'type' => 'password',
            'options' => [
                'label' => 'Confirm password'
            ],
            'attributes' => [
                'placeholder' => 'Confirm password...',
                //'required' => 'required',
            ]
        ], ['priority' => -20]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'username' => [
                'filters' => [
                    ['name' => 'StringTrim']
                ],
                'validators' => [
                    [
                        'name' => 'NotEmpty',
                        'break_chain_on_failure' => true,
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::USERNAME_EMPTY),
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 3,
                            'max' => 150,
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::USERNAME_LENGTH),
                        ],
                    ],
                    [
                        'name' => 'Regex',
                        'options' => [
                            'pattern' => '/^[a-zA-Z0-9-_.]+$/',
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::USERNAME_INVALID),
                        ]
                    ],
                    [
                        'name' => 'EmsNoRecordExists',
                        'options' => [
                            'field' => 'username',
                            'entity' => $this->userOptions->getUserEntity(),
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::USERNAME_TAKEN),
                        ]
                    ]
                ],
            ],
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
                                ->getMessage(MessagesOptions::EMAIL_EMPTY),
                        ]
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::EMAIL_INVALID),
                        ]
                    ],
                    [
                        'name' => 'EmsNoRecordExists',
                        'options' => [
                            'field' => 'email',
                            'entity' => $this->userOptions->getUserEntity(),
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::EMAIL_TAKEN),
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
                                ->getMessage(MessagesOptions::PASSWORD_EMPTY),
                        ]
                    ],
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 4,
                            'max' => 150,
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::PASSWORD_LENGTH),
                        ]
                    ]
                ]
            ],
            'passwordConfirm' => [
                'validators' => [
                    [
                        'name' => 'Identical',
                        'options' => [
                            'token' => 'password',
                            'message' => $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::PASSWORD_MISMATCH),
                        ]
                    ]
                ]
            ],
        ];
    }
}
