<?php
/**
 * Created by PhpStorm.
 * User: n3vra
 * Date: 6/20/2016
 * Time: 8:08 PM
 */

namespace Dot\User\Options;

use Dot\User\Entity\UserEntity;
use Dot\User\Entity\UserEntityHydrator;
use Dot\User\Exception\InvalidArgumentException;
use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class UserOptions
 * @package Dot\User\Options
 */
class UserOptions extends AbstractOptions
{
    /** @var  string */
    protected $userEntity = UserEntity::class;

    /** @var  string */
    protected $userEntityHydrator = UserEntityHydrator::class;

    /** @var int  */
    protected $passwordCost = 11;

    /** @var bool  */
    protected $enableUserStatus = true;

    /** @var array  */
    protected $userListeners = [];

    /** @var bool  */
    protected $showFormInputLabels = false;

    /** @var  DbOptions */
    protected $dbOptions;

    /** @var  LoginOptions */
    protected $loginOptions;

    /** @var  RegisterOptions */
    protected $registerOptions;

    /** @var  PasswordRecoveryOptions */
    protected $passwordRecoveryOptions;

    /** @var  ConfirmAccountOptions */
    protected $confirmAccountOptions;

    /** @var  MessagesOptions */
    protected $messagesOptions;

    protected $__strictMode__ = false;

    /**
     * @return string
     */
    public function getUserEntity()
    {
        return $this->userEntity;
    }

    /**
     * @param string $userEntity
     * @return UserOptions
     */
    public function setUserEntity($userEntity)
    {
        $this->userEntity = $userEntity;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserEntityHydrator()
    {
        return $this->userEntityHydrator;
    }

    /**
     * @param string $userEntityHydrator
     * @return UserOptions
     */
    public function setUserEntityHydrator($userEntityHydrator)
    {
        $this->userEntityHydrator = $userEntityHydrator;
        return $this;
    }

    /**
     * @return int
     */
    public function getPasswordCost()
    {
        return $this->passwordCost;
    }

    /**
     * @param int $passwordCost
     * @return UserOptions
     */
    public function setPasswordCost($passwordCost)
    {
        $this->passwordCost = $passwordCost;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnableUserStatus()
    {
        return $this->enableUserStatus;
    }

    /**
     * @param boolean $enableUserStatus
     * @return UserOptions
     */
    public function setEnableUserStatus($enableUserStatus)
    {
        $this->enableUserStatus = $enableUserStatus;
        return $this;
    }

    /**
     * @return array
     */
    public function getUserListeners()
    {
        return $this->userListeners;
    }

    /**
     * @param array $userListeners
     * @return UserOptions
     */
    public function setUserListeners($userListeners)
    {
        $this->userListeners = (array) $userListeners;
        return $this;
    }


    /**
     * @return DbOptions
     */
    public function getDbOptions()
    {
        if(!$this->dbOptions) {
            $this->setDbOptions([]);
        }
        return $this->dbOptions;
    }

    /**
     * @param DbOptions|array $dbOptions
     * @return UserOptions
     */
    public function setDbOptions($dbOptions)
    {
        if(is_array($dbOptions)) {
            $this->dbOptions = new DbOptions($dbOptions);
        }
        elseif($dbOptions instanceof DbOptions){
            $this->dbOptions = $dbOptions;
        }
        else {
            throw new InvalidArgumentException(sprintf(
                'DbOptions should be an array or an %s object. %s provided.',
                DbOptions::class,
                is_object($dbOptions) ? get_class($dbOptions) : gettype($dbOptions)
            ));
        }

        return $this;
    }

    /**
     * @return LoginOptions
     */
    public function getLoginOptions()
    {
        if(!$this->loginOptions) {
            $this->setLoginOptions([]);
        }
        return $this->loginOptions;
    }

    /**
     * @param LoginOptions|array $loginOptions
     * @return UserOptions
     */
    public function setLoginOptions($loginOptions)
    {
        if(is_array($loginOptions)) {
            $this->loginOptions = new LoginOptions($loginOptions);
        }
        elseif($loginOptions instanceof LoginOptions){
            $this->loginOptions = $loginOptions;
        }
        else {
            throw new InvalidArgumentException(sprintf(
                'LoginOptions should be an array or an %s object. %s provided.',
                LoginOptions::class,
                is_object($loginOptions) ? get_class($loginOptions) : gettype($loginOptions)
            ));
        }

        return $this;
    }

    /**
     * @return RegisterOptions
     */
    public function getRegisterOptions()
    {
        if(!$this->registerOptions) {
            $this->setRegisterOptions([]);
        }

        return $this->registerOptions;
    }

    /**
     * @param RegisterOptions|array $registerOptions
     * @return UserOptions
     */
    public function setRegisterOptions($registerOptions)
    {
        if(is_array($registerOptions)) {
            $this->registerOptions = new RegisterOptions($registerOptions);
        }
        elseif($registerOptions instanceof RegisterOptions){
            $this->registerOptions = $registerOptions;
        }
        else {
            throw new InvalidArgumentException(sprintf(
                'RegisterOptions should be an array or an %s object. %s provided.',
                RegisterOptions::class,
                is_object($registerOptions) ? get_class($registerOptions) : gettype($registerOptions)
            ));
        }

        return $this;
    }

    /**
     * @return PasswordRecoveryOptions
     */
    public function getPasswordRecoveryOptions()
    {
        if(!$this->passwordRecoveryOptions) {
            $this->setPasswordRecoveryOptions([]);
        }

        return $this->passwordRecoveryOptions;
    }

    /**
     * @param PasswordRecoveryOptions|array $passwordRecoveryOptions
     * @return UserOptions
     */
    public function setPasswordRecoveryOptions($passwordRecoveryOptions)
    {
        if(is_array($passwordRecoveryOptions)) {
            $this->passwordRecoveryOptions = new PasswordRecoveryOptions($passwordRecoveryOptions);
        }
        elseif($passwordRecoveryOptions instanceof PasswordRecoveryOptions){
            $this->passwordRecoveryOptions = $passwordRecoveryOptions;
        }
        else {
            throw new InvalidArgumentException(sprintf(
                'PasswordRecoveryOptions should be an array or an %s object. %s provided.',
                PasswordRecoveryOptions::class,
                is_object($passwordRecoveryOptions) ? get_class($passwordRecoveryOptions) : gettype($passwordRecoveryOptions)
            ));
        }
        return $this;
    }

    /**
     * @return ConfirmAccountOptions
     */
    public function getConfirmAccountOptions()
    {
        if(!$this->confirmAccountOptions) {
            $this->setConfirmAccountOptions([]);
        }

        return $this->confirmAccountOptions;
    }

    /**
     * @param ConfirmAccountOptions|array $confirmAccountOptions
     * @return UserOptions
     */
    public function setConfirmAccountOptions($confirmAccountOptions)
    {
        if(is_array($confirmAccountOptions)) {
            $this->confirmAccountOptions = new ConfirmAccountOptions($confirmAccountOptions);
        }
        elseif($confirmAccountOptions instanceof ConfirmAccountOptions){
            $this->confirmAccountOptions = $confirmAccountOptions;
        }
        else {
            throw new InvalidArgumentException(sprintf(
                'ConfirmAccountOptions should be an array or an %s object. %s provided.',
                ConfirmAccountOptions::class,
                is_object($confirmAccountOptions) ? get_class($confirmAccountOptions) : gettype($confirmAccountOptions)
            ));
        }
        return $this;
    }

    /**
     * @return MessagesOptions
     */
    public function getMessagesOptions()
    {
        if(!$this->messagesOptions) {
            $this->setMessagesOptions([]);
        }
        return $this->messagesOptions;
    }

    /**
     * @param MessagesOptions|array $messagesOptions
     * @return UserOptions
     */
    public function setMessagesOptions($messagesOptions)
    {
        if(is_array($messagesOptions)) {
            $this->messagesOptions = new MessagesOptions($messagesOptions);
        }
        elseif($messagesOptions instanceof MessagesOptions){
            $this->messagesOptions = $messagesOptions;
        }
        else {
            throw new InvalidArgumentException(sprintf(
                'MessagesOptions should be an array or an %s object. %s provided.',
                MessagesOptions::class,
                is_object($messagesOptions) ? get_class($messagesOptions) : gettype($messagesOptions)
            ));
        }
        return $this;
    }



    /**
     * @return boolean
     */
    public function isShowFormInputLabels()
    {
        return $this->showFormInputLabels;
    }

    /**
     * @param boolean $showFormInputLabels
     * @return UserOptions
     */
    public function setShowFormInputLabels($showFormInputLabels)
    {
        $this->showFormInputLabels = $showFormInputLabels;
        return $this;
    }

    
    
}