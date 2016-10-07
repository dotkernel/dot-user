<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/6/2016
 * Time: 8:17 PM
 */

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class ConfirmAccountOptions
 * @package Dot\User\Options
 */
class ConfirmAccountOptions extends AbstractOptions
{
    /** @var bool  */
    protected $enableAccountConfirmation = true;

    /** @var  mixed */
    protected $activeUserStatus = 'active';

    /**
     * @return boolean
     */
    public function isEnableAccountConfirmation()
    {
        return $this->enableAccountConfirmation;
    }

    /**
     * @param boolean $enableAccountConfirmation
     * @return ConfirmAccountOptions
     */
    public function setEnableAccountConfirmation($enableAccountConfirmation)
    {
        $this->enableAccountConfirmation = $enableAccountConfirmation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActiveUserStatus()
    {
        return $this->activeUserStatus;
    }

    /**
     * @param mixed $activeUserStatus
     * @return ConfirmAccountOptions
     */
    public function setActiveUserStatus($activeUserStatus)
    {
        $this->activeUserStatus = $activeUserStatus;
        return $this;
    }

}