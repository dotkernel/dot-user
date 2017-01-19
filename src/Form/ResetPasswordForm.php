<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/26/2016
 * Time: 8:48 PM
 */

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;

/**
 * Class ResetPasswordForm
 * @package Dot\User\Form
 */
class ResetPasswordForm extends Form
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /**
     * ResetPasswordForm constructor.
     * @param UserOptions $userOptions
     * @param string $name
     * @param array $options
     */
    public function __construct(UserOptions $userOptions, $name = 'reset-password', array $options = [])
    {
        $this->userOptions = $userOptions;
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add(array(
            'type' => 'password',
            'name' => 'newPassword',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => array(
                'placeholder' => 'New Password',
                //'required' => true,
            ),
        ));

        $this->add(array(
            'type' => 'password',
            'name' => 'newPasswordVerify',
            'options' => [
                'label' => 'Confirm Password',
            ],
            'attributes' => array(
                'placeholder' => 'Confirm Password',
                //'required' => true,
            ),
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Reset password',
            ),
        ), ['priority' => -100]);

        $csrf = new Csrf('reset_password_csrf', [
            'csrf_options' => [
                'timeout' => $this->userOptions->getFormCsrfTimeout(),
                'message' => $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::MESSAGE_CSRF_EXPIRED)
            ]
        ]);
        $this->add($csrf);

        $this->getEventManager()->trigger('init', $this);
    }
}
