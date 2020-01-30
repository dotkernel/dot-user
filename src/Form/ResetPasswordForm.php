<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Dot\User\Options\UserOptionsAwareTrait;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilter;

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
            'type' => 'submit',
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
