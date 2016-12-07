<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-frontend
 * @author: n3vrax
 * Date: 7/18/2016
 * Time: 9:55 PM
 */

namespace Dot\User\Form;

use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Form\FormInterface;

/**
 * Class UserForm
 * @package Dot\Frontend\User\Form
 */
class UserForm extends Form
{
    use EventManagerAwareTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /** @var  Fieldset */
    protected $userFieldset;

    /** @var array  */
    protected $currentValidationGroup = [
        'id' => true, 'username' => true, 'email' => true, 'password' => true, 'passwordVerify' => true
    ];

    /**
     * UserForm constructor.
     * @param UserOptions $userOptions
     * @param Fieldset $userFieldset
     * @param array $options
     */
    public function __construct(
        UserOptions $userOptions,
        Fieldset $userFieldset,
        array $options = [])
    {
        $this->userOptions = $userOptions;
        $this->userFieldset = $userFieldset;
        parent::__construct('user_form', $options);
    }

    public function init()
    {
        $this->userFieldset->setName('user');
        $this->userFieldset->setUseAsBaseFieldset(true);

        $this->add($this->userFieldset);

        $this->add([
            'type' => 'Csrf',
            'name' => 'update_account_csrf',
            'options' => [
                'csrf_options' => [
                    'timeout' => $this->userOptions->getFormCsrfTimeout(),
                    'message' => $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::MESSAGE_CSRF_EXPIRED)
                ]
            ]
        ]);

        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Save'
            ],
            ['priority' => -100]
        ]);

        $this->getEventManager()->trigger('init', $this);
    }

    /**
     * @param array $currentValidationGroup
     * @return $this
     */
    public function setCurrentValidationGroup(array $currentValidationGroup)
    {
        $this->currentValidationGroup = $currentValidationGroup;
        return $this;
    }

    /**
     * @return array
     */
    public function getCurrentValidationGroup()
    {
        return $this->currentValidationGroup;
    }

    public function removeUsernameValidation()
    {
        $this->currentValidationGroup['username'] = false;
    }

    public function removeEmailValidation()
    {
        $this->currentValidationGroup['email'] = false;
    }

    public function resetValidationGroup()
    {
        foreach ($this->currentValidationGroup as $key => $value) {
            $this->currentValidationGroup[$key] = true;
        }
        $this->setValidationGroup(FormInterface::VALIDATE_ALL);
    }

    public function applyValidationGroup()
    {
        $validationGroup = $this->getActiveValidationGroup($this->currentValidationGroup);
        $this->setValidationGroup(['user' => $validationGroup]);
    }

    public function getActiveValidationGroup($groups)
    {
        $validationGroup = [];
        foreach ($groups as $key => $value) {
            if(is_array($value)) {
                $validationGroup[$key] = $this->getActiveValidationGroup($value);
            }
            elseif($value === true) {
                $validationGroup[] = $key;
            }
        }
        return $validationGroup;
    }
}