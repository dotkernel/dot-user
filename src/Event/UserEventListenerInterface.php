<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 2:12 AM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface UserEventListenerInterface
 * @package Dot\User\Event
 */
interface UserEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeRegistration(UserEvent $e);

    public function onAfterRegistration(UserEvent $e);

    public function onRegistrationError(UserEvent $e);

    public function onBeforeAccountConfirmation(UserEvent $e);

    public function onAfterAccountConfirmation(UserEvent $e);

    public function onAccountConfirmationError(UserEvent $e);

    public function onBeforeOptOut(UserEvent $e);

    public function onAfterOptOut(UserEvent $e);

    public function onOptOutError(UserEvent $e);

    public function onBeforePasswordReset(UserEvent $e);

    public function onAfterPasswordReset(UserEvent $e);

    public function onResetPasswordError(UserEvent $e);

    public function onBeforeAccountUpdate(UserEvent $e);

    public function onAfterAccountUpdate(UserEvent $e);

    public function onAccountUpdateError(UserEvent $e);

    public function onBeforeChangePassword(UserEvent $e);

    public function onAfterChangePassword(UserEvent $e);

    public function onChangePasswordError(UserEvent $e);

    public function onBeforeDelete(UserEvent $e);

    public function onAfterDelete(UserEvent $e);

    public function onDeleteError(UserEvent $e);
}
