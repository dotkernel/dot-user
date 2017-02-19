<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 2:27 AM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class UserEventListenerTrait
 * @package Dot\User\Event
 */
trait UserEventListenerTrait
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_BEFORE_REGISTRATION,
            [$this, 'onBeforeRegistration'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_AFTER_REGISTRATION,
            [$this, 'onAfterRegistration'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_REGISTRATION_ERROR,
            [$this, 'onRegistrationError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_BEFORE_ACCOUNT_CONFIRMATION,
            [$this, 'onBeforeAccountConfirmation'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_AFTER_ACCOUNT_CONFIRMATION,
            [$this, 'onAfterAccountConfirmation'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_ACCOUNT_CONFIRMATION_ERROR,
            [$this, 'onAccountConfirmationError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_BEFORE_PASSWORD_RESET,
            [$this, 'onBeforePasswordReset'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_AFTER_PASSWORD_RESET,
            [$this, 'onAfterPasswordReset'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_RESET_PASSWORD_ERROR,
            [$this, 'onResetPasswordError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_BEFORE_ACCOUNT_UPDATE,
            [$this, 'onBeforeAccountUpdate'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_AFTER_ACCOUNT_UPDATE,
            [$this, 'onAfterAccountUpdate'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_ACCOUNT_UPDATE_ERROR,
            [$this, 'onAccountUpdateError'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_BEFORE_DELETE,
            [$this, 'onBeforeDelete'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_AFTER_DELETE,
            [$this, 'onAfterDelete'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserEvent::EVENT_USER_DELETE_ERROR,
            [$this, 'onDeleteError'],
            $priority
        );
    }

    public function onBeforeRegistration(UserEvent $e)
    {
        // no-op
    }

    public function onAfterRegistration(UserEvent $e)
    {
        // no-op
    }

    public function onRegistrationError(UserEvent $e)
    {
        // no-op
    }

    public function onBeforeAccountConfirmation(UserEvent $e)
    {
        // no-op
    }

    public function onAfterAccountConfirmation(UserEvent $e)
    {
        // no-op
    }

    public function onAccountConfirmationError(UserEvent $e)
    {
        // no-op
    }

    public function onBeforePasswordReset(UserEvent $e)
    {
        // no-op
    }

    public function onAfterPasswordReset(UserEvent $e)
    {
        // no-op
    }

    public function onResetPasswordError(UserEvent $e)
    {
        // no-op
    }

    public function onBeforeAccountUpdate(UserEvent $e)
    {
        // no-op
    }

    public function onAfterAccountUpdate(UserEvent $e)
    {
        // no-op
    }

    public function onAccountUpdateError(UserEvent $e)
    {
        // no-op
    }

    public function onBeforeDelete(UserEvent $e)
    {
        // no-op
    }

    public function onAfterDelete(UserEvent $e)
    {
        // no-op
    }

    public function onDeleteError(UserEvent $e)
    {
        //no-op
    }
}
