<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface ControllerEventListenerInterface
 * @package Dot\User\Event
 */
interface UserControllerEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeChangePasswordRender(UserControllerEvent $e);

    public function onBeforeRegisterRender(UserControllerEvent $e);

    public function onBeforeAccountRender(UserControllerEvent $e);

    public function onBeforeResetPasswordRender(UserControllerEvent $e);

    public function onBeforeForgotPasswordRender(UserControllerEvent $e);

    public function onBeforeAccountUpdateFormValidation(UserControllerEvent $e);
}
