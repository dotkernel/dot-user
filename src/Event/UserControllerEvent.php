<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Dot\Event\Event;

/**
 * Class ControllerEvent
 * @package Dot\User\Event
 */
class UserControllerEvent extends Event
{
    const EVENT_CONTROLLER_BEFORE_CHANGE_PASSWORD_RENDER = 'event.controller.beforeChangePasswordRender';
    const EVENT_CONTROLLER_BEFORE_REGISTER_RENDER = 'event.controller.beforeRegisterRender';
    const EVENT_CONTROLLER_BEFORE_ACCOUNT_RENDER = 'event.controller.beforeAccountRender';
    const EVENT_CONTROLLER_BEFORE_RESET_PASSWORD_RENDER = 'event.controller.beforeResetPasswordRender';
    const EVENT_CONTROLLER_BEFORE_FORGOT_PASSWORD_RENDER = 'event.controller.beforeForgotPasswordRender';

    const EVENT_CONTROLLER_BEFORE_ACCOUNT_UPDATE_FORM_VALIDATION = 'event.controller.beforeAccountUpdateFormValidation';
}
