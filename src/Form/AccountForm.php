<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 2/5/2017
 * Time: 3:22 AM
 */

declare(strict_types = 1);

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Dot\User\Options\UserOptionsAwareTrait;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class AccountForm
 * @package Dot\User\Form
 */
class AccountForm extends Form implements UserOptionsAwareInterface
{
    use UserOptionsAwareTrait;

    public function __construct()
    {
        parent::__construct('accountForm');

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
            'name' => 'account_csrf',
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
                'value' => 'Update account'
            ]
        ], ['priority' => -100]);

        $this->setValidationGroup([
            'account_csrf',
            'user' => [
                'id',
                'username',
            ]
        ]);
    }
}
