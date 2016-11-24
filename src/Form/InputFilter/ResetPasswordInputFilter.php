<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/26/2016
 * Time: 8:54 PM
 */

namespace Dot\User\Form\InputFilter;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

/**
 * Class ResetPasswordInputFilter
 * @package Dot\User\Form\InputFilter
 */
class ResetPasswordInputFilter extends InputFilter
{
    /** @var  UserOptions */
    protected $options;

    /**
     * ResetPasswordInputFilter constructor.
     * @param UserOptions $options
     */
    public function __construct(
        UserOptions $options
    ) {
        $this->options = $options;
    }

    public function init()
    {
        $this->add([
            'name' => 'newPassword',
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_EMPTY_PASSWORD)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'max' => 150,
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_PASSWORD_CHARACTER_LIMIT)
                    ],
                ],
            ],
        ]);

        $this->add([
            'name' => 'newPasswordVerify',
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_EMPTY_PASSWORD_VERIFY)
                    ]
                ],
                [
                    'name' => 'Identical',
                    'options' => [
                        'token' => 'newPassword',
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_PASSWORD_MISMATCH)
                    ],
                ],
            ],
        ]);
    }
}