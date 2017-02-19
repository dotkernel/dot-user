<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/4/2017
 * Time: 12:37 AM
 */

declare(strict_types = 1);

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;
use Zend\Stdlib\ArrayUtils;

/**
 * Class MessagesOptions
 * @package Dot\User\Options
 */
class MessagesOptions extends AbstractOptions
{
    /** login messages */
    const IDENTITY_EMPTY = 0;
    const PASSWORD_EMPTY = 5;
    const PASSWORD_LENGTH = 10;
    const PASSWORD_MISMATCH = 11;
    const CURRENT_PASSWORD_EMPTY = 12;
    const CURRENT_PASSWORD_INVALID = 13;

    const USERNAME_EMPTY = 15;
    const USERNAME_LENGTH = 20;
    const USERNAME_INVALID = 25;
    const USERNAME_TAKEN = 30;

    const EMAIL_EMPTY = 35;
    const EMAIL_INVALID = 40;
    const EMAIL_TAKEN = 45;

    const CONFIRM_TOKEN_SAVE_ERROR = 50;
    const CONFIRM_ACCOUNT_ERROR = 51;
    const CONFIRM_ACCOUNT_INVALID_TOKEN = 52;
    const CONFIRM_ACCOUNT_INVALID_EMAIL = 53;
    const CONFIRM_ACCOUNT_SUCCESS = 54;
    const CONFIRM_ACCOUNT_DISABLED = 55;

    const REMEMBER_TOKEN_SAVE_ERROR = 60;
    const REMEMBER_TOKEN_INVALID = 61;

    const RESET_TOKEN_SAVE_ERROR = 70;
    const RESET_PASSWORD_ERROR = 71;
    const RESET_TOKEN_EXPIRED = 72;
    const RESET_TOKEN_INVALID = 73;
    const RESET_PASSWORD_INVALID_EMAIL = 74;
    const RESET_PASSWORD_SUCCESS = 95;
    const RESET_PASSWORD_DISABLED = 96;

    const CHANGE_PASSWORD_ERROR = 75;
    const CHANGE_PASSWORD_SUCCESS = 76;

    const USER_DELETE_ERROR = 80;
    const USER_REGISTER_ERROR = 81;
    const USER_UPDATE_ERROR = 82;
    const USER_UPDATE_SUCCESS = 83;
    const USER_NOT_FOUND = 84;

    const REGISTER_DISABLED = 90;
    const REGISTER_SUCCESS = 91;
    const FORGOT_PASSWORD_SUCCESS = 92;

    const FORM_EXPIRED = 100;
    const UNAUTHORIZED = 105;
    const ACCOUNT_INACTIVE = 110;

    /** @var array */
    protected $messages = [
        MessagesOptions::IDENTITY_EMPTY => 'Identity is required and cannot be empty',
        MessagesOptions::PASSWORD_EMPTY => 'Password is required and cannot be empty',
        MessagesOptions::PASSWORD_LENGTH => 'Password must have between 4 and 150 characters',
        MessagesOptions::PASSWORD_MISMATCH => 'Password confirm does not match',
        MessagesOptions::CURRENT_PASSWORD_EMPTY => 'Current password is required and cannot be empty',
        MessagesOptions::CURRENT_PASSWORD_INVALID => 'Current password is not valid',

        MessagesOptions::USERNAME_EMPTY => 'Username is required and cannot be empty',
        MessagesOptions::USERNAME_INVALID => 'Username contains invalid characters',
        MessagesOptions::USERNAME_LENGTH => 'Username must have between 3 and 150 characters',
        MessagesOptions::USERNAME_TAKEN => 'Username is already taken',

        MessagesOptions::EMAIL_EMPTY => 'Email address is required and cannot be empty',
        MessagesOptions::EMAIL_INVALID => 'Email address is not valid',
        MessagesOptions::EMAIL_TAKEN => 'Email address is already registered',

        MessagesOptions::CONFIRM_ACCOUNT_ERROR => 'Account confirmation failed. Please try again',
        MessagesOptions::CONFIRM_ACCOUNT_INVALID_EMAIL => 'Account confirmation failed. Please try again',
        MessagesOptions::CONFIRM_ACCOUNT_INVALID_TOKEN => 'Account confirmation failed. Please try again',
        MessagesOptions::CONFIRM_TOKEN_SAVE_ERROR => 'Account creation request has failed. Please try again',
        MessagesOptions::CONFIRM_ACCOUNT_SUCCESS => 'Account was successfully confirmed. You may sign in now',
        MessagesOptions::CONFIRM_ACCOUNT_DISABLED => 'Account confirmation is disabled',

        MessagesOptions::REMEMBER_TOKEN_SAVE_ERROR =>
            'Remember me feature has encountered and error. This will not affect general usability',
        MessagesOptions::REMEMBER_TOKEN_INVALID => 'Remember me token is not valid',

        MessagesOptions::RESET_TOKEN_SAVE_ERROR => 'Could not register the reset password request. Please try again',
        MessagesOptions::RESET_PASSWORD_ERROR => 'Failed to update account password. Please try again',
        MessagesOptions::RESET_TOKEN_EXPIRED => 'Reset token has expired. Please submit another password reset request',
        MessagesOptions::RESET_TOKEN_INVALID => 'Reset token is not valid',
        MessagesOptions::RESET_PASSWORD_INVALID_EMAIL => 'There is no account registered with the given email address',
        MessagesOptions::RESET_PASSWORD_SUCCESS => 'Password was successfully reset',
        MessagesOptions::RESET_PASSWORD_DISABLED => 'Password recovery is disabled',

        MessagesOptions::CHANGE_PASSWORD_ERROR => 'Change password has failed. Please try again',
        MessagesOptions::CHANGE_PASSWORD_SUCCESS => 'Password was successfully updated',

        MessagesOptions::USER_DELETE_ERROR => 'Could not delete user account',
        MessagesOptions::USER_REGISTER_ERROR => 'Account creation has failed. Please try again',
        MessagesOptions::USER_UPDATE_ERROR => 'Account update has failed. Please try again',
        MessagesOptions::USER_UPDATE_SUCCESS => 'Account information was successfully updated',
        MessagesOptions::USER_NOT_FOUND => 'Could not get the currently authenticated user',

        MessagesOptions::REGISTER_DISABLED => 'Account registration is disabled',
        MessagesOptions::REGISTER_SUCCESS => 'You account was successfully created',
        MessagesOptions::FORGOT_PASSWORD_SUCCESS => 'Success! Check your email for further instructions',

        MessagesOptions::FORM_EXPIRED => 'The form CSRF has expired and was refreshed. Try again now',
        MessagesOptions::UNAUTHORIZED => 'You must sign in first in order to access the requested content',
        MessagesOptions::ACCOUNT_INACTIVE => 'You account is inactive or it may not have been confirmed'
    ];

    /**
     * MessagesOptions constructor.
     * @param null $options
     */
    public function __construct($options = null)
    {
        $this->__strictMode__ = false;
        parent::__construct($options);
    }

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
