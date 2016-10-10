<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/8/2016
 * Time: 6:59 PM
 */

namespace Dot\User\Event;

/**
 * Class PasswordResetEvent
 * @package Dot\User\Event
 */
class PasswordResetEvent extends AbstractUserEvent
{
    const EVENT_PASSWORD_RESET_TOKEN_PRE = 'event.user.password_reset.token.pre';
    const EVENT_PASSWORD_RESET_TOKEN_POST = 'event.user.password_reset.token.post';
    const EVENT_PASSWORD_RESET_TOKEN_ERROR = 'event.user.password_reset.token.error';

    const EVENT_PASSWORD_RESET_PRE = 'event.user.password_reset.pre';
    const EVENT_PASSWORD_RESET_POST = 'event.user.password_reset.post';
    const EVENT_PASSWORD_RESET_ERROR = 'event.user.password_reset.error';

    /** @var  mixed */
    protected $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return PasswordResetEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }


}