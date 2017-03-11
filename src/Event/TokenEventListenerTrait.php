<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class TokenEventListenerTrait
 * @package Dot\User\Event
 */
trait TokenEventListenerTrait
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_BEFORE_SAVE_CONFIRM_TOKEN,
            [$this, 'onBeforeSaveConfirmToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_AFTER_SAVE_CONFIRM_TOKEN,
            [$this, 'onAfterSaveConfirmToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_CONFIRM_TOKEN_SAVE_ERROR,
            [$this, 'onConfirmTokenSaveError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_BEFORE_SAVE_REMEMBER_TOKEN,
            [$this, 'onBeforeSaveRememberToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_AFTER_SAVE_REMEMBER_TOKEN,
            [$this, 'onAfterSaveRememberToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_REMEMBER_TOKEN_SAVE_ERROR,
            [$this, 'onRememberTokenSaveError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_BEFORE_VALIDATE_REMEMBER_TOKEN,
            [$this, 'onBeforeValidateRememberToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_AFTER_VALIDATE_REMEMBER_TOKEN,
            [$this, 'onAfterValidateRememberToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_REMEMBER_TOKEN_VALIDATION_ERROR,
            [$this, 'onRememberTokenValidationError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_BEFORE_SAVE_RESET_TOKEN,
            [$this, 'onBeforeSaveResetToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_AFTER_SAVE_RESET_TOKEN,
            [$this, 'onAfterSaveResetToken'],
            $priority
        );
        $this->listeners[] = $events->attach(
            TokenEvent::EVENT_TOKEN_RESET_TOKEN_SAVE_ERROR,
            [$this, 'onResetTokenSaveError'],
            $priority
        );
    }

    public function onBeforeSaveConfirmToken(TokenEvent $e)
    {
        // no-op
    }

    public function onAfterSaveConfirmToken(TokenEvent $e)
    {
        // no-op
    }

    public function onConfirmTokenSaveError(TokenEvent $e)
    {
        // no-op
    }

    public function onBeforeSaveRememberToken(TokenEvent $e)
    {
        // no-op
    }

    public function onAfterSaveRememberToken(TokenEvent $e)
    {
        // no-op
    }

    public function onRememberTokenSaveError(TokenEvent $e)
    {
        // no-op
    }

    public function onBeforeValidateRememberToken(TokenEvent $e)
    {
        // no-op
    }

    public function onAfterValidateRememberToken(TokenEvent $e)
    {
        // no-op
    }

    public function onRememberTokenValidationError(TokenEvent $e)
    {
        // no-op
    }

    public function onBeforeSaveResetToken(TokenEvent $e)
    {
        // no-op
    }

    public function onAfterSaveResetToken(TokenEvent $e)
    {
        // no-op
    }

    public function onResetTokenSaveError(TokenEvent $e)
    {
        // no-op
    }
}
