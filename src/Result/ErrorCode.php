<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 4:44 AM
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
    const USER_PASSWORD_INVALID = 'user.passwordInvalid';
}
