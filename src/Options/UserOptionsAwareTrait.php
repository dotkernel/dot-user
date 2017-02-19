<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/17/2017
 * Time: 9:26 PM
 */

declare(strict_types = 1);

namespace Dot\User\Options;

/**
 * Class UserOptionsAwareTrait
 * @package Dot\User\Options
 */
trait UserOptionsAwareTrait
{
    /** @var  UserOptions */
    protected $userOptions;

    /**
     * @param UserOptions $userOptions
     */
    public function setUserOptions(UserOptions $userOptions)
    {
        $this->userOptions = $userOptions;
    }

    /**
     * @return UserOptions
     */
    public function getUserOptions(): UserOptions
    {
        return $this->userOptions;
    }
}
