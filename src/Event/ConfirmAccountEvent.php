<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/6/2016
 * Time: 7:34 PM
 */

namespace Dot\User\Event;

/**
 * Class ConfirmAccountEvent
 * @package Dot\User\Event
 */
class ConfirmAccountEvent extends AbstractUserEvent
{
    const EVENT_CONFIRM_ACCOUNT_PRE = 'event.user.confirm_account.pre';
    const EVENT_CONFIRM_ACCOUNT_POST = 'event.user.confirm_account.post';
    const EVENT_CONFIRM_ACCOUNT_ERROR = 'event.user.confirm_account.error';

    const EVENT_CONFIRM_ACCOUNT_TOKEN_PRE = 'event.user.confirm_account.token.pre';
    const EVENT_CONFIRM_ACCOUNT_TOKEN_POST = 'event.user.confirm_account.token.post';
    const EVENT_CONFIRM_ACCOUNT_TOKEN_ERROR = 'event.user.confirm_account.token.error';

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
     * @return ConfirmAccountEvent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }


}