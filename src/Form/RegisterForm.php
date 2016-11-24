<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 8:05 PM
 */

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Element\Captcha;
use Zend\Form\Element\Csrf;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * Class RegisterForm
 * @package Dot\User\Form
 */
class RegisterForm extends Form
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /** @var  Fieldset */
    protected $userFieldset;

    /** @var  InputFilter */
    protected $userInputFilter;

    /** @var  Captcha */
    protected $captcha;

    /**
     * RegisterForm constructor.
     * @param UserOptions $userOptions
     * @param Fieldset $userFieldset
     * @param InputFilter $userInputFilter
     * @param array $options
     */
    public function __construct(
        UserOptions $userOptions,
        Fieldset $userFieldset,
        InputFilter $userInputFilter,
        $options = array()
    ) {
        $this->userOptions = $userOptions;
        $this->userFieldset = $userFieldset;
        $this->userInputFilter = $userInputFilter;
        parent::__construct('user_register_form', $options);
    }

    public function init()
    {
        $this->userFieldset->setName('user');
        $this->userFieldset->setUseAsBaseFieldset(true);

        if ($this->userOptions->getRegisterOptions()->isEnableUsername()) {
            $this->userFieldset->remove('username');
        }

        $this->add($this->userFieldset);
        $this->getInputFilter()->add($this->userInputFilter, 'user');

        if ($this->userOptions->getRegisterOptions()->isUseRegistrationFormCaptcha()) {
            //add captcha element
            $this->add([
                'type' => 'Captcha',
                'name' => 'captcha',
                'options' => [
                    'label' => 'Please verify you are human',
                    'captcha' => $this->userOptions->getRegisterOptions()->getFormCaptchaOptions()
                ]
            ], ['priority' => -99]);
        }

        $csrf = new Csrf('register_csrf', [
            'csrf_options' => [
                'timeout' => $this->userOptions->getRegisterOptions()->getUserFormTimeout(),
                'message' => $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::MESSAGE_CSRF_EXPIRED)
            ]
        ]);
        $this->add($csrf);

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => array(
                'value' => 'Sign Up',
            ),
        ), ['priority' => -100]);

        if ($this->userOptions->getRegisterOptions()->isUseRegistrationFormCaptcha() && $this->captcha) {
            $this->add($this->captcha, ['name' => 'captcha']);
        }

        $this->getEventManager()->trigger('init', $this);
    }

    /**
     * @return Captcha
     */
    public function getCaptchaElement()
    {
        return $this->captcha;
    }

    /**
     * @param Captcha $captcha
     * @return RegisterForm
     */
    public function setCaptchaElement(Captcha $captcha)
    {
        $this->captcha = $captcha;
        return $this;
    }


}