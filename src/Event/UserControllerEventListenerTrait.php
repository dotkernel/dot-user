<?php
/**
 * @copyright: DotKernel
 * @library: dot-user
 * @author: n3vrax
 * Date: 2/21/2017
 * Time: 9:04 PM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class ControllerEventListenerTrait
 * @package Dot\User\Event
 */
trait UserControllerEventListenerTrait
{
    use ListenerAggregateTrait;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(
            UserControllerEvent::EVENT_CONTROLLER_BEFORE_ACCOUNT_UPDATE_FORM_VALIDATION,
            [$this, 'onBeforeAccountUpdateFormValidation'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserControllerEvent::EVENT_CONTROLLER_BEFORE_CHANGE_PASSWORD_RENDER,
            [$this, 'onBeforeChangePasswordRender'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserControllerEvent::EVENT_CONTROLLER_BEFORE_FORGOT_PASSWORD_RENDER,
            [$this, 'onBeforeForgotPasswordRender'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserControllerEvent::EVENT_CONTROLLER_BEFORE_RESET_PASSWORD_RENDER,
            [$this, 'onBeforeResetPasswordRender'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserControllerEvent::EVENT_CONTROLLER_BEFORE_ACCOUNT_RENDER,
            [$this, 'onBeforeAccountRender'],
            $priority
        );
        $this->listeners[] = $events->attach(
            UserControllerEvent::EVENT_CONTROLLER_BEFORE_REGISTER_RENDER,
            [$this, 'onBeforeRegisterRender'],
            $priority
        );
    }

    public function onBeforeChangePasswordRender(UserControllerEvent $e)
    {
        // no-op
    }

    public function onBeforeRegisterRender(UserControllerEvent $e)
    {
        // no-op
    }

    public function onBeforeAccountRender(UserControllerEvent $e)
    {
        //no-op
    }

    public function onBeforeResetPasswordRender(UserControllerEvent $e)
    {
        //no-op
    }

    public function onBeforeForgotPasswordRender(UserControllerEvent $e)
    {
        //no-op
    }

    public function onBeforeAccountUpdateFormValidation(UserControllerEvent $e)
    {
        //no-op
    }
}
