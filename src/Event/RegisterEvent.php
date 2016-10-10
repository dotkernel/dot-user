<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/6/2016
 * Time: 7:21 PM
 */

namespace Dot\User\Event;

/**
 * Class RegisterEvent
 * @package Dot\User\Event
 */
class RegisterEvent extends AbstractUserEvent
{
    const EVENT_REGISTER_PRE = 'event.user.register.pre';
    const EVENT_REGISTER_POST = 'event.user.register.post';
    const EVENT_REGISTER_ERROR = 'event.user.register.error';

}