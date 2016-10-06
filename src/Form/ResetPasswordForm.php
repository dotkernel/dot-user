<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/26/2016
 * Time: 8:48 PM
 */

namespace Dot\User\Form;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;

/**
 * Class ResetPasswordForm
 * @package Dot\User\Form
 */
class ResetPasswordForm extends Form
{
    use EventManagerAwareTrait;

    /**
     * ResetPasswordForm constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'reset-password', array $options = [])
    {
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

        $this->getEventManager()->trigger('init', $this);
    }
}