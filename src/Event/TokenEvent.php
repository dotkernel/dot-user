<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 1:58 AM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Dot\Event\Event;

/**
 * Class TokenEvent
 * @package Dot\User\Event
 */
class TokenEvent extends Event
{
    const EVENT_TOKEN_BEFORE_SAVE_CONFIRM_TOKEN = 'event.token.beforeSaveConfirmToken';
    const EVENT_TOKEN_AFTER_SAVE_CONFIRM_TOKEN = 'event.token.afterSaveConfirmToken';
    const EVENT_TOKEN_CONFIRM_TOKEN_SAVE_ERROR = 'event.token.confirmTokenSaveError';

    const EVENT_TOKEN_BEFORE_SAVE_REMEMBER_TOKEN = 'event.token.beforeSaveRememberToken';
    const EVENT_TOKEN_AFTER_SAVE_REMEMBER_TOKEN = 'event.token.afterSaveRememberToken';
    const EVENT_TOKEN_REMEMBER_TOKEN_SAVE_ERROR = 'event.token.rememberTokenSaveError';

    const EVENT_TOKEN_BEFORE_VALIDATE_REMEMBER_TOKEN = 'event.token.beforeValidateRememberToken';
    const EVENT_TOKEN_AFTER_VALIDATE_REMEMBER_TOKEN = 'event.token.afterValidateRememberToken';
    const EVENT_TOKEN_REMEMBER_TOKEN_VALIDATION_ERROR = 'event.token.rememberTokenValidationError';

    const EVENT_TOKEN_BEFORE_SAVE_RESET_TOKEN = 'event.token.beforeSaveResetToken';
    const EVENT_TOKEN_AFTER_SAVE_RESET_TOKEN = 'event.token.afterSaveResetToken';
    const EVENT_TOKEN_RESET_TOKEN_SAVE_ERROR = 'event.token.resetTokenSaveError';
}
