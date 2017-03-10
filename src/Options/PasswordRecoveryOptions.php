<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/14/2017
 * Time: 9:00 PM
 */

declare(strict_types = 1);

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class PasswordRecoveryOptions
 * @package Dot\User\Options
 */
class PasswordRecoveryOptions extends AbstractOptions
{
    /** @var bool */
    protected $enableRecovery = true;

    /** @var int */
    protected $resetTokenTimeout = 3600;

    /**
     * @return bool
     */
    public function isEnableRecovery(): bool
    {
        return $this->enableRecovery;
    }

    /**
     * @param bool $enableRecovery
     */
    public function setEnableRecovery(bool $enableRecovery)
    {
        $this->enableRecovery = $enableRecovery;
    }

    /**
     * @return int
     */
    public function getResetTokenTimeout(): int
    {
        return $this->resetTokenTimeout;
    }

    /**
     * @param int $resetTokenTimeout
     */
    public function setResetTokenTimeout(int $resetTokenTimeout)
    {
        $this->resetTokenTimeout = $resetTokenTimeout;
    }
}
