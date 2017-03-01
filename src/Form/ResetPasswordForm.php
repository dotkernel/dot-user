<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 2/5/2017
 * Time: 4:26 AM
 */

declare(strict_types = 1);

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Dot\User\Options\UserOptionsAwareTrait;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class ResetPasswordForm
 * @package Dot\User\Form
 */
class ResetPasswordForm extends Form implements UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

    /**
     * ResetPasswordForm constructor.
     */
    public function __construct()
    {
        parent::__construct('resetPasswordForm');

        $this->setAttribute('method', 'post');
        $this->setInputFilter(new InputFilter());
    }

    public function init()
    {
        $this->add([
            'type' => 'UserFieldset',
            'options' => [
                'use_as_base_fieldset' => true,
            ]
        ]);

        $this->add([
            'name' => 'reset_password_csrf',
            'type' => 'Csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => 3600,
                    'message' => $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::FORM_EXPIRED)
                ]
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Update password'
            ]
        ]);

        $this->setValidationGroup([
            'reset_password_csrf',
            'user' => [
                'password',
                'passwordConfirm'
            ]
        ]);
    }
}
