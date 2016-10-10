<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/21/2016
 * Time: 9:33 PM
 */

namespace Dot\User\Form\InputFilter;

use Dot\User\Options\MessageOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\InputFilter\InputFilter;

/**
 * Class LoginInputFilter
 * @package Dot\User\Form\InputFilter
 */
class LoginInputFilter extends InputFilter
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $options;

    /**
     * LoginInputFilter constructor.
     * @param UserOptions $options
     */
    public function __construct(UserOptions $options)
    {
        $this->options = $options;
    }

    public function init()
    {
        $this->add([
            'name' => 'identity',
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessageOptions()
                            ->getMessage(MessageOptions::MESSAGE_LOGIN_EMPTY_IDENTITY)
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'password',
            'filters' => [],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'break_chain_on_failure' => true,
                    'options' => [
                        'message' => $this->options->getMessageOptions()
                            ->getMessage(MessageOptions::MESSAGE_LOGIN_EMPTY_PASSWORD)
                    ]
                ],
                [
                    'name' => 'StringLength',
                    'options' => [
                        'min' => 4,
                        'message' => $this->options->getMessageOptions()
                            ->getMessage(MessageOptions::MESSAGE_LOGIN_PASSWORD_TOO_SHORT)
                    ]
                ]
            ],
        ]);

        $this->getEventManager()->trigger('init', $this);
    }
}