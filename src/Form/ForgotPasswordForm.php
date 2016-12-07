<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/14/2016
 * Time: 8:35 PM
 */

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Element\Csrf;
use Zend\Form\Form;

/**
 * Class ForgotPasswordForm
 * @package Dot\User\Form
 */
class ForgotPasswordForm extends Form
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /**
     * ForgotPasswordForm constructor.
     * @param UserOptions $userOptions
     * @param string $name
     * @param array $options
     */
    public function __construct(UserOptions $userOptions, $name = 'forgot-password', array $options = [])
    {
        $this->userOptions = $userOptions;
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

        $csrf = new Csrf('forgot_password_csrf', [
            'csrf_options' => [
                'timeout' => $this->userOptions->getFormCsrfTimeout(),
                'message' => $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::MESSAGE_CSRF_EXPIRED)
            ]
        ]);
        $this->add($csrf);

        $this->getEventManager()->trigger('init', $this);
    }
}