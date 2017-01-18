<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 11/24/2016
 * Time: 7:47 PM
 */

namespace Dot\User\Form\Fieldset;

use Zend\Form\Fieldset;

/**
 * Class UserFieldset
 * @package Dot\User\Form\Fieldset
 */
class UserFieldset extends Fieldset
{
    /**
     * AdminFieldset constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'user_fieldset', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'placeholder' => 'Email...'
            ]
        ]);

        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
            'attributes' => [
                'id' => 'username',
                'placeholder' => 'Username...'
            ]
        ]);

        $this->add(array(
            'type' => 'password',
            'name' => 'password',
            'options' => [
                'label' => 'Password'
            ],
            'attributes' => array(
                'placeholder' => 'Password',
                //'required' => true,
            ),
        ), ['priority' => -10]);

        $this->add(array(
            'type' => 'password',
            'name' => 'passwordVerify',
            'options' => [
                'label' => 'Confirm Password'
            ],
            'attributes' => array(
                'placeholder' => 'Confirm Password',
                //'required' => true,
            ),
        ), ['priority' => -11]);
    }
}
