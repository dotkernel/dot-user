<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
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
