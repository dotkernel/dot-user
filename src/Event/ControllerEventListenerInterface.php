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
interface ControllerEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeChangePasswordRender(ControllerEvent $e);

    public function onBeforeRegisterRender(ControllerEvent $e);

    public function onBeforeAccountRender(ControllerEvent $e);

    public function onBeforeResetPasswordRender(ControllerEvent $e);

    public function onBeforeForgotPasswordRender(ControllerEvent $e);

    public function onBeforeAccountUpdateFormValidation(ControllerEvent $e);
}
