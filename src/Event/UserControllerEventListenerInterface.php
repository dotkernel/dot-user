<?php
/**
 * @copyright: DotKernel
 * @library: dot-user
 * @author: n3vrax
 * Date: 2/21/2017
 * Time: 9:01 PM
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
