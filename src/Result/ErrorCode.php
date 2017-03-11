<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Result;

/**
 * Class Error
 * @package Dot\User\Result
 */
class ErrorCode
{
    const TOKEN_SAVE_ERROR = 'token.saveError';
    const TOKEN_INVALID = 'token.invalid';
    const TOKEN_NOT_FOUND = 'token.notFound';
    const TOKEN_EXPIRED = 'token.expired';

    const USER_SAVE_ERROR = 'user.saveError';
    const USER_NOT_FOUND = 'user.notFound';
    const USER_DELETE_ERROR = 'user.deleteError';
    const USER_PASSWORD_INVALID = 'user.passwordInvalid';
}
