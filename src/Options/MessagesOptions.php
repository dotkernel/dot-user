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
    const PASSWORD_CONFIRM_EMPTY = 14;
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

    const REGISTER_DISABLED = 90;
    const REGISTER_SUCCESS = 91;
    const FORGOT_PASSWORD_SUCCESS = 92;

    const FORM_EXPIRED = 100;
    const UNAUTHORIZED = 105;
    const ACCOUNT_LOGIN_STATUS_NOT_ALLOWED = 110;
    const ACCOUNT_UNCONFIRMED = 111;
    const ACCOUNT_INVALID = 115;

    const OPT_OUT_SUCCESS = 120;
    const OPT_OUT_ERROR = 121;
    const OPT_OUT_INVALID_PARAMS = 122;

    const SIGN_OUT_FIRST = 125;

    /** @var array */
    protected $messages = [
        MessagesOptions::IDENTITY_EMPTY => '<b>Identity</b> is required and cannot be empty',
        MessagesOptions::PASSWORD_EMPTY => '<b>Password</b> is required and cannot be empty',
        MessagesOptions::PASSWORD_LENGTH => '<b>Password</b> must have between 4 and 150 characters',
        MessagesOptions::PASSWORD_MISMATCH => '<b>Password confirm</b> does not match',
        MessagesOptions::PASSWORD_CONFIRM_EMPTY => '<b>Password confirmation</b> is required and cannot be empty',
        MessagesOptions::CURRENT_PASSWORD_EMPTY => '<b>Current password</b> is required and cannot be empty',
        MessagesOptions::CURRENT_PASSWORD_INVALID => '<b>Current password</b> is not valid',

        MessagesOptions::USERNAME_EMPTY => '<b>Username</b> is required and cannot be empty',
        MessagesOptions::USERNAME_INVALID => '<b>Username</b> contains invalid characters',
        MessagesOptions::USERNAME_LENGTH => '<b>Username</b> must have between 3 and 150 characters',
        MessagesOptions::USERNAME_TAKEN => '<b>Username</b> cannot be used as it\'s already taken',

        MessagesOptions::EMAIL_EMPTY => '<b>E-mail address</b> is required and cannot be empty',
        MessagesOptions::EMAIL_INVALID => '<b>E-mail address</b> is not valid',
        MessagesOptions::EMAIL_TAKEN => '<b>E-mail address</b> is already registered with an account',

        MessagesOptions::CONFIRM_ACCOUNT_ERROR => 'Account activation failed. Please try again or contact us',
        MessagesOptions::CONFIRM_ACCOUNT_INVALID_EMAIL => 'Account activation failed due to invalid parameters',
        MessagesOptions::CONFIRM_ACCOUNT_INVALID_TOKEN => 'Account activation failed due to invalid parameters',
        MessagesOptions::CONFIRM_TOKEN_SAVE_ERROR =>
            'Account activation link could not be generated. Please try again',
        MessagesOptions::CONFIRM_ACCOUNT_SUCCESS => 'Your account was successfully activated. You may sign in now',

        MessagesOptions::REMEMBER_TOKEN_SAVE_ERROR =>
            'Remember me feature has encountered and error. This will not affect general usability',
        MessagesOptions::REMEMBER_TOKEN_INVALID => 'Remember me token is not valid',

        MessagesOptions::RESET_TOKEN_SAVE_ERROR => 'Could not register the reset password request. Please try again',
        MessagesOptions::RESET_PASSWORD_ERROR => 'Failed to update account password. Please try again',
        MessagesOptions::RESET_TOKEN_EXPIRED => 'Reset token has expired. Please submit another password reset request',
        MessagesOptions::RESET_TOKEN_INVALID => 'Reset token is not valid anymore',
        MessagesOptions::RESET_PASSWORD_INVALID_EMAIL => 'There is no account registered with the given email address',
        MessagesOptions::RESET_PASSWORD_SUCCESS => 'Password was successfully reset',
        MessagesOptions::RESET_PASSWORD_DISABLED => 'Password recovery is disabled',

        MessagesOptions::CHANGE_PASSWORD_ERROR => 'Change password has failed. Please try again',
        MessagesOptions::CHANGE_PASSWORD_SUCCESS => 'Password was successfully updated',

        MessagesOptions::USER_DELETE_ERROR => 'Could not delete user account',
        MessagesOptions::USER_REGISTER_ERROR => 'Account creation has failed. Please try again',
        MessagesOptions::USER_UPDATE_ERROR => 'Account update has failed. Please try again',
        MessagesOptions::USER_UPDATE_SUCCESS => 'Account information was successfully updated',

        MessagesOptions::REGISTER_DISABLED => 'Account registration is disabled',
        MessagesOptions::REGISTER_SUCCESS => 'Your account was successfully created',
        MessagesOptions::FORGOT_PASSWORD_SUCCESS => 'Password recovery e-mail was sent to %s',

        MessagesOptions::FORM_EXPIRED => 'The form CSRF has expired and was refreshed. Try again now',
        MessagesOptions::UNAUTHORIZED => 'You must sign in first in order to access the requested content',
        MessagesOptions::ACCOUNT_LOGIN_STATUS_NOT_ALLOWED =>
            'Your account is inactive or it may not have been confirmed',
        MessagesOptions::ACCOUNT_UNCONFIRMED => 'Your account needs to be activated first',
        MessagesOptions::ACCOUNT_INVALID => 'Your account had been disabled or deleted',

        MessagesOptions::OPT_OUT_ERROR => 'Account failed to be un-registered. Please try again or contact us',
        MessagesOptions::OPT_OUT_SUCCESS => 'Account was successfully un-registered',
        MessagesOptions::OPT_OUT_INVALID_PARAMS => 'Account has failed to be un-registered due to invalid parameters',

        MessagesOptions::SIGN_OUT_FIRST => 'Sign out first in order to access the requested link'
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
