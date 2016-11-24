<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/14/2016
 * Time: 8:39 PM
 */

namespace Dot\User\Form\InputFilter;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

/**
 * Class ForgotPasswordInputFilter
 * @package Dot\User\Form\InputFilter
 */
class ForgotPasswordInputFilter extends InputFilter
{
    /** @var  UserOptions */
    protected $options;

    /**
     * ForgotPasswordInputFilter constructor.
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
                            ->getMessage(MessagesOptions::MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL)
                    ]
                ],
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'message' => $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_FORGOT_PASSWORD_INVALID_EMAIL),
                    ]
                ],
            ],
        ]);
    }
}