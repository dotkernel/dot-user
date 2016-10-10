<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/20/2016
 * Time: 4:29 PM
 */

namespace Dot\User\Form\InputFilter;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

/**
 * Class ChangePasswordInputFilter
 * @package Dot\User\Form\InputFilter
 */
class ChangePasswordInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $options;

    /**
     * ChangePasswordInputFilter constructor.
     * @param UserOptions $options
     */
    public function __construct(UserOptions $options)
    {
        $this->options = $options;
    }

    public function init()
    {
        $this->add([
            'name' => 'password',
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_PASSWORD_EMPTY)
                    ]
                ],
            ]
        ]);

        $this->add([
            'name' => 'newPassword',
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_NEW_PASSWORD_EMPTY)
                    ]
                ],
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_NEW_PASSWORD_TOO_SHORT)
                    ],
                ],
            ]
        ]);

        $this->add([
            'name' => 'newPasswordVerify',
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_CONFIRM_EMPTY)
                    ]
                ],
                [
                    'name'    => 'Identical',
                    'options' => [
                        'token' => 'newPassword',
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_CONFIRM_MISMATCH)
                    ],
                ],
            ]
        ]);

        $this->getEventManager()->trigger('init', $this);
    }
}