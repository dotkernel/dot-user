<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 1:33 AM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Dot\Event\Event;

/**
 * Class UserEvent
 * @package Dot\User\Event
 */
class UserEvent extends Event
{
    const EVENT_USER_BEFORE_REGISTRATION = 'event.user.beforeRegistration';
    const EVENT_USER_AFTER_REGISTRATION = 'event.user.afterRegistration';
    const EVENT_USER_REGISTRATION_ERROR = 'event.user.registrationError';

    const EVENT_USER_BEFORE_ACCOUNT_CONFIRMATION = 'event.user.beforeAccountConfirmation';
    const EVENT_USER_AFTER_ACCOUNT_CONFIRMATION = 'event.user.afterAccountConfirmation';
    const EVENT_USER_ACCOUNT_CONFIRMATION_ERROR = 'event.user.accountConfirmationError';

    const EVENT_USER_BEFORE_PASSWORD_RESET = 'event.user.beforePasswordReset';
    const EVENT_USER_AFTER_PASSWORD_RESET = 'event.user.afterPasswordReset';
    const EVENT_USER_RESET_PASSWORD_ERROR = 'event.user.resetPasswordError';

    const EVENT_USER_BEFORE_CHANGE_PASSWORD = 'event.user.beforeChangePassword';
    const EVENT_USER_AFTER_CHANGE_PASSWORD = 'event.user.afterChangePassword';
    const EVENT_USER_CHANGE_PASSWORD_ERROR = 'event.user.changePasswordError';

    const EVENT_USER_BEFORE_ACCOUNT_UPDATE = 'event.user.beforeAccountUpdate';
    const EVENT_USER_AFTER_ACCOUNT_UPDATE = 'event.user.afterAccountUpdate';
    const EVENT_USER_ACCOUNT_UPDATE_ERROR = 'event.user.accountUpdateError';

    const EVENT_USER_BEFORE_DELETE = 'event.user.beforeDelete';
    const EVENT_USER_AFTER_DELETE = 'event.user.afterDelete';
    const EVENT_USER_DELETE_ERROR = 'event.user.deleteError';
}
