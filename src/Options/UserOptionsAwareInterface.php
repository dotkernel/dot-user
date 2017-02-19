<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/17/2017
 * Time: 9:25 PM
 */

declare(strict_types = 1);

namespace Dot\User\Options;

/**
 * Interface UserOptionsAwareInterface
 * @package Dot\User\Options
 */
interface UserOptionsAwareInterface
{
    /**
     * @param UserOptions $userOptions
     */
    public function setUserOptions(UserOptions $userOptions);

    /**
     * @return UserOptions
     */
    public function getUserOptions(): UserOptions;
}
