<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/20/2016
 * Time: 4:17 PM
 */

namespace Dot\User\Form;

use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;

/**
 * Class ChangePasswordForm
 * @package Dot\User\Form
 */
class ChangePasswordForm extends Form
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /**
     * ChangePasswordForm constructor.
     * @param UserOptions $userOptions
     * @param string $name
     * @param array $options
     */
    public function __construct(
        UserOptions $userOptions,
        $name = 'change-password',
        array $options = [])
    {
        $this->userOptions = $userOptions;
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Current Password'
            ],
            'attributes' => [
                'placeholder' => 'Current Password'
            ]
        ]);

        $this->add([
            'name' => 'newPassword',
            'type' => 'password',
            'options' => [
                'label' => 'New Password'
            ],
            'attributes' => [
                'placeholder' => 'New Password'
            ]
        ]);

        $this->add([
            'name' => 'newPasswordVerify',
            'type' => 'password',
            'options' => [
                'label' => 'Confirm New Password'
            ],
            'attributes' => [
                'placeholder' => 'Confirm New Password'
            ]
        ]);

        $csrf = new Csrf('change_password_csrf', [
            'csrf_options' => [
                'timeout' => $this->userOptions->getRegisterOptions()->getUserFormTimeout()
            ]
        ]);
        $this->add($csrf);

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Change Password',
            ),
        ), ['priority' => -100]);


        $this->getEventManager()->trigger('init', $this);
    }
}