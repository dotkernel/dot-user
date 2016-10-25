<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 10/6/2016
 * Time: 7:40 PM
 */

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class MessageOptions
 * @package Dot\User\Options
 */
class MessagesOptions extends AbstractOptions
{
    /** Confirm account related messages constant */
    const MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS = 1;
    const MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL = 2;
    const MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN = 3;
    const MESSAGE_CONFIRM_ACCOUNT_DISABLED = 4;
    const MESSAGE_CONFIRM_ACCOUNT_ERROR = 5;
    const MESSAGE_CONFIRM_ACCOUNT_SUCCESS = 6;

    /** login related messages */
    const MESSAGE_LOGIN_EMPTY_IDENTITY = 7;
    const MESSAGE_LOGIN_EMPTY_PASSWORD = 8;
    const MESSAGE_LOGIN_PASSWORD_TOO_SHORT = 9;
    const MESSAGE_LOGIN_ACCOUNT_INACTIVE = 10;
    const MESSAGE_REMEMBER_TOKEN_GENERATE_ERROR = 11;
    const MESSAGE_REMEMBER_TOKEN_REMOVE_ERROR = 12;

    /** password reset related messages */
    const MESSAGE_RESET_PASSWORD_INVALID_EMAIL = 13;
    const MESSAGE_RESET_PASSWORD_INVALID_TOKEN = 14;
    const MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED = 15;
    const MESSAGE_RESET_PASSWORD_MISSING_PARAMS = 16;
    const MESSAGE_RESET_PASSWORD_EMPTY_PASSWORD = 17;
    const MESSAGE_RESET_PASSWORD_TOO_SHORT = 18;
    const MESSAGE_RESET_PASSWORD_EMPTY_VERIFY = 19;
    const MESSAGE_RESET_PASSWORD_MISMATCH = 20;
    const MESSAGE_RESET_PASSWORD_DISABLED = 21;
    const MESSAGE_RESET_PASSWORD_ERROR = 22;
    const MESSAGE_RESET_PASSWORD_SUCCESS = 23;

    const MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL = 24;
    const MESSAGE_FORGOT_PASSWORD_ERROR = 25;
    const MESSAGE_FORGOT_PASSWORD_SUCCESS = 26;
    const MESSAGE_FORGOT_PASSWORD_INVALID_EMAIL = 27;

    /** register messages constants */
    const MESSAGE_REGISTER_EMPTY_EMAIL = 28;
    const MESSAGE_REGISTER_INVALID_EMAIL = 29;
    const MESSAGE_REGISTER_EMAIL_ALREADY_REGISTERED = 30;
    const MESSAGE_REGISTER_EMPTY_USERNAME = 31;
    const MESSAGE_REGISTER_USERNAME_TOO_SHORT = 32;
    const MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS = 33;
    const MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED = 34;
    const MESSAGE_REGISTER_EMPTY_PASSWORD = 35;
    const MESSAGE_REGISTER_PASSWORD_TOO_SHORT = 36;
    const MESSAGE_REGISTER_EMPTY_PASSWORD_CONFIRM = 37;
    const MESSAGE_REGISTER_PASSWORD_CONFIRM_NOT_MATCH = 38;
    const MESSAGE_REGISTER_ERROR = 39;
    const MESSAGE_REGISTER_SUCCESS = 40;

    /** other user messages */
    const MESSAGE_CHANGE_PASSWORD_OK = 41;
    const MESSAGE_CHANGE_PASSWORD_INVALID_USER = 42;
    const MESSAGE_CHANGE_PASSWORD_INVALID_CURRENT_PASSWORD = 43;
    const MESSAGE_CHANGE_PASSWORD_ERROR = 44;
    const MESSAGE_CHANGE_PASSWORD_PASSWORD_EMPTY = 45;
    const MESSAGE_CHANGE_PASSWORD_NEW_PASSWORD_EMPTY = 46;
    const MESSAGE_CHANGE_PASSWORD_NEW_PASSWORD_TOO_SHORT = 47;
    const MESSAGE_CHANGE_PASSWORD_CONFIRM_EMPTY = 48;
    const MESSAGE_CHANGE_PASSWORD_CONFIRM_MISMATCH = 49;

    protected $__strictMode__ = false;

    protected $messages = [
        /** account confirmation messages */
        MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_DISABLED => 'Account confirmation is disabled',
        MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_ERROR => 'Account confirmation error. Please try again',
        MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL => 'Account confirmation invalid parameters',
        MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN => 'Account confirmation invalid parameters',
        MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS => 'Account confirmation invalid parameters',
        MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_SUCCESS => 'Account successfully confirmed. You may sign in now',

        /** user login messages */
        MessagesOptions::MESSAGE_LOGIN_EMPTY_IDENTITY => 'Identity is required and cannot be empty',
        MessagesOptions::MESSAGE_LOGIN_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        MessagesOptions::MESSAGE_LOGIN_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
        MessagesOptions::MESSAGE_LOGIN_ACCOUNT_INACTIVE => 'Account is not active or it has not been confirmed',
        MessagesOptions::MESSAGE_REMEMBER_TOKEN_GENERATE_ERROR => [
            'Remember me feature encountered an error.',
            'This will not affect the general usability of the website'
        ],
        MessagesOptions::MESSAGE_REMEMBER_TOKEN_REMOVE_ERROR => 'Remember me token remove error',

        /** password recovery messages */
        MessagesOptions::MESSAGE_FORGOT_PASSWORD_ERROR => 'Password reset request error. Please try again',
        MessagesOptions::MESSAGE_FORGOT_PASSWORD_MISSING_EMAIL => 'Email address is required and cannot be empty',
        MessagesOptions::MESSAGE_FORGOT_PASSWORD_SUCCESS => [
            'Password reset request successfully registered',
            'You\'ll receive in email with further instructions'
        ],
        MessagesOptions::MESSAGE_FORGOT_PASSWORD_INVALID_EMAIL => 'Email address format is not valid',

        MessagesOptions::MESSAGE_RESET_PASSWORD_DISABLED => 'Password recovery is disabled',
        MessagesOptions::MESSAGE_RESET_PASSWORD_ERROR => 'Password reset error. Please try again',
        MessagesOptions::MESSAGE_RESET_PASSWORD_INVALID_EMAIL => 'Password reset error. Invalid parameters',
        MessagesOptions::MESSAGE_RESET_PASSWORD_INVALID_TOKEN => 'Password reset error. Invalid parameters',
        MessagesOptions::MESSAGE_RESET_PASSWORD_MISSING_PARAMS => 'Password reset error. Invalid parameters',
        MessagesOptions::MESSAGE_RESET_PASSWORD_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        MessagesOptions::MESSAGE_RESET_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
        MessagesOptions::MESSAGE_RESET_PASSWORD_EMPTY_VERIFY => 'Password confirmation is required and cannot be empty',
        MessagesOptions::MESSAGE_RESET_PASSWORD_MISMATCH => 'Password confirmation does not match',
        MessagesOptions::MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED => 'Password reset error. Reset token has expired',
        MessagesOptions::MESSAGE_RESET_PASSWORD_SUCCESS => 'Account password successfully updated',

        /** register messages constants */
        MessagesOptions::MESSAGE_REGISTER_EMPTY_EMAIL => 'Email address is required and cannot be empty',
        MessagesOptions::MESSAGE_REGISTER_INVALID_EMAIL => 'Email address format is not valid',
        MessagesOptions::MESSAGE_REGISTER_EMAIL_ALREADY_REGISTERED => 'Email address is already in use',
        MessagesOptions::MESSAGE_REGISTER_EMPTY_USERNAME => 'Username is required and cannot be empty',
        MessagesOptions::MESSAGE_REGISTER_USERNAME_TOO_SHORT => 'Username must have at least 4 characters',
        MessagesOptions::MESSAGE_REGISTER_USERNAME_INVALID_CHARACTERS => 'Username contains invalid characters',
        MessagesOptions::MESSAGE_REGISTER_USERNAME_ALREADY_REGISTERED => 'Username is already in use',
        MessagesOptions::MESSAGE_REGISTER_EMPTY_PASSWORD => 'Password is required and cannot be empty',
        MessagesOptions::MESSAGE_REGISTER_PASSWORD_TOO_SHORT => 'Password must have at least 4 characters',
        MessagesOptions::MESSAGE_REGISTER_EMPTY_PASSWORD_CONFIRM => 'Password confirmation is required',
        MessagesOptions::MESSAGE_REGISTER_PASSWORD_CONFIRM_NOT_MATCH => 'The two passwords do not match',
        MessagesOptions::MESSAGE_REGISTER_ERROR => 'Registration error. Please try again',
        MessagesOptions::MESSAGE_REGISTER_SUCCESS => 'Account successfully created',

        /** other user messages */
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_OK => 'Password successfully updated!',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_INVALID_USER => 'Change password error. Invalid authenticated user',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_INVALID_CURRENT_PASSWORD => 'Invalid current password provided',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_ERROR => 'Change password error. Please try again.',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_PASSWORD_EMPTY => 'Password is required and cannot be empty',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_NEW_PASSWORD_EMPTY => 'New password is required and cannot be empty',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_NEW_PASSWORD_TOO_SHORT => 'New password must contain at least 4 characters',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_CONFIRM_EMPTY => 'Password verify is required and cannot be empty',
        MessagesOptions::MESSAGE_CHANGE_PASSWORD_CONFIRM_MISMATCH => 'Password verify does not match'
    ];


    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param $messages
     * @return $this
     */
    public function setMessages($messages)
    {
        $this->messages = ArrayUtils::merge($this->messages, $messages, true);
        return $this;
    }

    /**
     * @param $key
     * @return mixed|string
     */
    public function getMessage($key)
    {
        return isset($this->messages[$key]) ? $this->messages[$key] : null;
    }
}