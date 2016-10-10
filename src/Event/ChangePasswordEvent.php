<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 8/10/2016
 * Time: 11:48 PM
 */

namespace Dot\User\Event;

/**
 * Class ChangePasswordEvent
 * @package Dot\User\Event
 */
class ChangePasswordEvent extends AbstractUserEvent
{
    const EVENT_CHANGE_PASSWORD_PRE = 'event.user.change_password.pre';
    const EVENT_CHANGE_PASSWORD_POST = 'event.user.change_password.post';
    const EVENT_CHANGE_PASSWORD_ERROR = 'event.user.change_password.error';
}