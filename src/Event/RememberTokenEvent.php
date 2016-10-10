<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/13/2016
 * Time: 8:04 PM
 */

namespace Dot\User\Event;

/**
 * Class RememberTokenEvent
 * @package Dot\User\Event
 */
class RememberTokenEvent extends AbstractUserEvent
{
    const EVENT_TOKEN_GENERATE_PRE = 'event.user.remember_token.generate.pre';
    const EVENT_TOKEN_GENERATE_POST = 'event.user.remember_token.generate.post';
    const EVENT_TOKEN_GENERATE_ERROR = 'event.user.remember_token.generate.error';

    const EVENT_TOKEN_REMOVE_PRE = 'event.user.remember_token.remove.pre';
    const EVENT_TOKEN_REMOVE_POST = 'event.user.remember_token.remove.post';
    const EVENT_TOKEN_REMOVE_ERROR = 'event.user.remember_token.remove.error';

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
     * @return RememberTokenEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

}