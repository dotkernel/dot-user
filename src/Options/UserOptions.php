<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Options;

use Dot\User\Entity\ConfirmTokenEntity;
use Dot\User\Entity\RememberTokenEntity;
use Dot\User\Entity\ResetTokenEntity;
use Dot\User\Entity\RoleEntity;
use Dot\User\Entity\UserEntity;
use Zend\Stdlib\AbstractOptions;

/**
 * Class UserOptions
 * @package Dot\User\Options
 */
class UserOptions extends AbstractOptions
{
    /** @var  string */
    protected $userEntity = UserEntity::class;

    /** @var  string */
    protected $roleEntity = RoleEntity::class;

    /** @var  string */
    protected $confirmTokenEntity = ConfirmTokenEntity::class;

    /** @var  string */
    protected $resetTokenEntity = ResetTokenEntity::class;

    /** @var  string */
    protected $rememberTokenEntity = RememberTokenEntity::class;

    /** @var array */
    protected $defaultRoles = ['user'];

    /** @var int */
    protected $passwordCost = 11;

    /** @var bool */
    protected $enableAccountConfirmation = true;

    /** @var string */
    protected $confirmedAccountStatus = UserEntity::STATUS_ACTIVE;

    /** @var array */
    protected $eventListeners = [];

    /** @var  LoginOptions */
    protected $loginOptions;

    /** @var  RegisterOptions */
    protected $registerOptions;

    /** @var  PasswordRecoveryOptions */
    protected $passwordRecoveryOptions;

    /** @var  TemplateOptions */
    protected $templateOptions;

    /** @var  MessagesOptions */
    protected $messagesOptions;

    /** @var  array */
    protected $routeDefault = [];

    /**
     * @return string
     */
    public function getUserEntity(): string
    {
        return $this->userEntity;
    }

    /**
     * @param string $userEntity
     */
    public function setUserEntity(string $userEntity)
    {
        $this->userEntity = $userEntity;
    }

    /**
     * @return array
     */
    public function getDefaultRoles(): array
    {
        return $this->defaultRoles;
    }

    /**
     * @param array $defaultRoles
     */
    public function setDefaultRoles(array $defaultRoles)
    {
        $this->defaultRoles = $defaultRoles;
    }

    /**
     * @return int
     */
    public function getPasswordCost(): int
    {
        return $this->passwordCost;
    }

    /**
     * @param int $passwordCost
     */
    public function setPasswordCost(int $passwordCost)
    {
        $this->passwordCost = $passwordCost;
    }

    /**
     * @return array
     */
    public function getEventListeners(): array
    {
        return $this->eventListeners;
    }

    /**
     * @param array $eventListeners
     */
    public function setEventListeners(array $eventListeners)
    {
        $this->eventListeners = $eventListeners;
    }

    /**
     * @return string
     */
    public function getRoleEntity(): string
    {
        return $this->roleEntity;
    }

    /**
     * @param string $roleEntity
     */
    public function setRoleEntity(string $roleEntity)
    {
        $this->roleEntity = $roleEntity;
    }

    /**
     * @return string
     */
    public function getConfirmTokenEntity(): string
    {
        return $this->confirmTokenEntity;
    }

    /**
     * @param string $confirmTokenEntity
     */
    public function setConfirmTokenEntity(string $confirmTokenEntity)
    {
        $this->confirmTokenEntity = $confirmTokenEntity;
    }

    /**
     * @return string
     */
    public function getResetTokenEntity(): string
    {
        return $this->resetTokenEntity;
    }

    /**
     * @param string $resetTokenEntity
     */
    public function setResetTokenEntity(string $resetTokenEntity)
    {
        $this->resetTokenEntity = $resetTokenEntity;
    }

    /**
     * @return string
     */
    public function getRememberTokenEntity(): string
    {
        return $this->rememberTokenEntity;
    }

    /**
     * @param string $rememberTokenEntity
     */
    public function setRememberTokenEntity(string $rememberTokenEntity)
    {
        $this->rememberTokenEntity = $rememberTokenEntity;
    }

    /**
     * @return LoginOptions
     */
    public function getLoginOptions(): LoginOptions
    {
        return $this->loginOptions;
    }

    /**
     * @param array $loginOptions
     */
    public function setLoginOptions(array $loginOptions)
    {
        $this->loginOptions = new LoginOptions($loginOptions);
    }

    /**
     * @return PasswordRecoveryOptions
     */
    public function getPasswordRecoveryOptions(): PasswordRecoveryOptions
    {
        return $this->passwordRecoveryOptions;
    }

    /**
     * @param array $passwordRecoveryOptions
     */
    public function setPasswordRecoveryOptions(array $passwordRecoveryOptions)
    {
        $this->passwordRecoveryOptions = new PasswordRecoveryOptions($passwordRecoveryOptions);
    }

    /**
     * @return bool
     */
    public function isEnableAccountConfirmation(): bool
    {
        return $this->enableAccountConfirmation;
    }

    /**
     * @param bool $enableAccountConfirmation
     */
    public function setEnableAccountConfirmation(bool $enableAccountConfirmation)
    {
        $this->enableAccountConfirmation = $enableAccountConfirmation;
    }

    /**
     * @return string
     */
    public function getConfirmedAccountStatus(): string
    {
        return $this->confirmedAccountStatus;
    }

    /**
     * @param string $confirmedAccountStatus
     */
    public function setConfirmedAccountStatus(string $confirmedAccountStatus)
    {
        $this->confirmedAccountStatus = $confirmedAccountStatus;
    }

    /**
     * @return RegisterOptions
     */
    public function getRegisterOptions(): RegisterOptions
    {
        return $this->registerOptions;
    }

    /**
     * @param array $registerOptions
     */
    public function setRegisterOptions(array $registerOptions)
    {
        $this->registerOptions = new RegisterOptions($registerOptions);
    }

    /**
     * @return TemplateOptions
     */
    public function getTemplateOptions(): TemplateOptions
    {
        return $this->templateOptions;
    }

    /**
     * @param array $templateOptions
     */
    public function setTemplateOptions(array $templateOptions)
    {
        $this->templateOptions = new TemplateOptions($templateOptions);
    }

    /**
     * @return MessagesOptions
     */
    public function getMessagesOptions(): MessagesOptions
    {
        return $this->messagesOptions;
    }

    /**
     * @param array $messagesOptions
     */
    public function setMessagesOptions(array $messagesOptions)
    {
        $this->messagesOptions = new MessagesOptions($messagesOptions);
    }

    /**
     * @return array
     */
    public function getRouteDefault(): array
    {
        return $this->routeDefault;
    }

    /**
     * @param array $routeDefault
     */
    public function setRouteDefault(array $routeDefault)
    {
        $this->routeDefault = $routeDefault;
    }
}
