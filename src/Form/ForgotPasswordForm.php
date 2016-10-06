<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/14/2016
 * Time: 8:35 PM
 */

namespace Dot\User\Form;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Form;

/**
 * Class ForgotPasswordForm
 * @package Dot\User\Form
 */
class ForgotPasswordForm extends Form
{
    use EventManagerAwareTrait;

    /**
     * ForgotPasswordForm constructor.
     * @param string $name
     * @param array $options
     */
    public function __construct($name = 'forgot-password', array $options = [])
    {
        parent::__construct($name, $options);
    }

    public function init()
    {
        $this->add(array(
            'type' => 'text',
            'name' => 'email',
            'attributes' => array(
                'placeholder' => 'Your email address',
                //'required' => true,
            ),
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Request reset',
            ),
        ), ['priority' => -100]);

        $this->getEventManager()->trigger('init', $this);
    }
}