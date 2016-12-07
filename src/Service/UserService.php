<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 8:04 PM
 */

namespace Dot\User\Service;

use Dot\Authentication\AuthenticationInterface;
use Dot\Ems\Service\EntityService;
use Dot\Event\Event;
use Dot\Helpers\Psr7\HttpMessagesAwareInterface;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Event\ChangePasswordEvent;
use Dot\User\Event\ConfirmAccountEvent;
use Dot\User\Event\Listener\UserListenerAwareInterface;
use Dot\User\Event\Listener\UserListenerAwareTrait;
use Dot\User\Event\PasswordResetEvent;
use Dot\User\Event\RegisterEvent;
use Dot\User\Event\RememberTokenEvent;
use Dot\User\Event\UserUpdateEvent;
use Dot\User\Mapper\UserMapperInterface;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\ResultInterface;
use Dot\User\Result\UserOperationResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Crypt\Password\PasswordInterface;
use Zend\Math\Rand;

/**
 * Class UserService
 * @package Dot\User\Service
 */
class UserService extends EntityService  implements UserServiceInterface, UserListenerAwareInterface, HttpMessagesAwareInterface
{
    use UserListenerAwareTrait;

    /** @var  UserMapperInterface */
    protected $mapper;

    /** @var  UserOptions */
    protected $options;

    /** @var  PasswordInterface */
    protected $passwordService;

    /** @var  AuthenticationInterface */
    protected $authentication;

    /** @var  ServerRequestInterface */
    protected $request;

    /** @var  ResponseInterface */
    protected $response;

    /** @var  bool */
    protected $debug = false;

    /**
     * UserService constructor.
     * @param UserMapperInterface $mapper
     * @param UserOptions $options
     * @param PasswordInterface $password
     * @param AuthenticationInterface $authentication
     */
    public function __construct(
        UserMapperInterface $mapper,
        UserOptions $options,
        PasswordInterface $password,
        AuthenticationInterface $authentication)
    {
        parent::__construct($mapper);
        $this->options = $options;
        $this->passwordService = $password;
        $this->authentication = $authentication;
    }

    /**
     * Generates an auto-login token for the user, stores it in the backend and sets a login cookie
     *
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function generateRememberToken(UserEntityInterface $user)
    {
        $result = new UserOperationResult(true);
        $data = null;

        try {
            $selector = Rand::getString(32);
            $token = Rand::getString(32);

            $data = new \stdClass();
            $data->userId = $user->getId();
            $data->selector = $selector;
            $data->token = $token;

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_GENERATE_PRE,
                $user,
                $data
            ));

            //hash the token
            $dbData = (array)$data;
            $dbData['token'] = md5($dbData['token']);

            $this->mapper->saveRememberToken($dbData);

            $cookieData = base64_encode(serialize(['selector' => $selector, 'token' => $token]));

            $name = $this->options->getLoginOptions()->getRememberMeCookieName();
            $expire = $this->options->getLoginOptions()->getRememberMeCookieExpire();
            $secure = $this->options->getLoginOptions()->isRememberMeCookieSecure();

            setcookie($name, $cookieData, time() + $expire, "/", "", $secure, true);

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_GENERATE_POST,
                $user, $data
            ));
        } catch (\Exception $e) {
            error_log("Remember token generate error: " . $e->getMessage());

            $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REMEMBER_TOKEN_GENERATE_ERROR);
            $result = $this->createUserOperationResultWithException($e, $message, $user);

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_GENERATE_ERROR,
                $user, $data, $result
            ));
        }

        return $result;
    }

    /**
     * Validates a remember token coming from cookie
     *
     * @param $selector
     * @param $token
     * @return bool
     */
    public function checkRememberToken($selector, $token)
    {
        try {
            $r = $this->mapper->findRememberToken($selector);
            if ($r) {
                if ($r['token'] == md5($token)) {
                    return $r;
                } else {
                    //clear any tokens for this user as security measure
                    $user = $this->mapper->fetch([$this->getMapper()->getIdentifierName() => $r['userId']]);
                    if ($user) {
                        $this->removeRememberToken($user);
                    }
                }
            }
        } catch (\Exception $e) {
            error_log("Check remember token error: " . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Removes all remember tokens for a given user and also unset the corresponding cookie
     *
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function removeRememberToken(UserEntityInterface $user)
    {
        $result = new UserOperationResult(true);
        try {
            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_PRE,
                $user
            ));

            $this->mapper->removeRememberToken($user->getId());

            //clear cookies
            if (isset($_COOKIE[$this->options->getLoginOptions()->getRememberMeCookieName()])) {
                unset($_COOKIE[$this->options->getLoginOptions()->getRememberMeCookieName()]);
                setcookie($this->options->getLoginOptions()->getRememberMeCookieName(), '', time() - 3600, '/');
            }

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_POST,
                $user
            ));
        } catch (\Exception $e) {
            error_log("Remove remember token error for user " . $user->getId() . " with message: " . $e->getMessage());

            $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REMEMBER_TOKEN_REMOVE_ERROR);
            $result = $this->createUserOperationResultWithException($e, $message, $user);

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_ERROR,
                $user, null, $result
            ));
        }

        return $result;
    }


    /**
     * Change user status from unconfirmed to active based on an email and valid confirmation token
     *
     * @param $email
     * @param $token
     * @return UserOperationResult
     * @throws \Exception
     */
    public function confirmAccount($email, $token)
    {
        $result = new UserOperationResult(true, $this->options->getMessagesOptions()
            ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_SUCCESS));

        $user = null;

        try {
            if (empty($email) || empty($token)) {
                $result = $this->createUserOperationResultWithMessages(
                    $this->options->getMessagesOptions()
                        ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_MISSING_PARAMS)
                );
            } else {
                /** @var UserEntityInterface $user */
                $user = $this->mapper->fetch(['email' => $email]);
                if ($user) {
                    $r = $this->mapper->findConfirmToken($user->getId(), $token);
                    if ($r) {
                        $this->mapper->beginTransaction();

                        //trigger pre event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE, $user));

                        $user->setStatus($this->options->getConfirmAccountOptions()->getActiveUserStatus());
                        $this->save($user);

                        $this->mapper->removeConfirmToken($user->getId(), $token);

                        $this->mapper->commit();

                        //post confirm event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_POST, $user));
                    } else {
                        $result = $this->createUserOperationResultWithMessages(
                            $this->options->getMessagesOptions()
                                ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN)
                        );
                    }
                } else {
                    $result = $this->createUserOperationResultWithMessages(
                        $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL)
                    );
                }
            }
        } catch (\Exception $e) {
            error_log("Confirm account error: " . $e->getMessage(), E_USER_ERROR);

            $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_ERROR);
            $result = $this->createUserOperationResultWithException($e, $message, $user);

            //trigger error event
            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, $user, $result));

            $this->mapper->rollback();
            return $result;
        }

        return $result;
    }

    /**
     * Based on a user email, generate a token and store a hash of it with and expiration time
     * trigger a specific event, so mail service can send an email based on it
     *
     * @param $email
     * @return UserOperationResult
     */
    public function generateResetToken($email)
    {
        $result = new UserOperationResult(true, $this->options->getMessagesOptions()
            ->getMessage(MessagesOptions::MESSAGE_FORGOT_PASSWORD_SUCCESS));

        $user = null;
        $data = null;

        try {
            /** @var UserEntityInterface $user */
            $user = $this->find(['email' => $email]);

            if ($user) {
                $data = new \stdClass();
                $data->userId = $user->getId();
                $data->token = md5(Rand::getString(32) . time() . $email);
                $data->expireAt = time() + $this->options->getPasswordRecoveryOptions()
                        ->getResetPasswordTokenTimeout();

                $this->getEventManager()->triggerEvent(
                    $this->createPasswordResetEvent(
                        PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_PRE,
                        $user,
                        $data
                    ));

                $this->mapper->saveResetToken((array)$data);

                $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                    PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_POST,
                    $user,
                    $data
                ));
            }
        } catch (\Exception $e) {
            error_log("Password reset request error: " . $e->getMessage());

            $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_FORGOT_PASSWORD_ERROR);
            $result = $this->createUserOperationResultWithException($e, $message, $user);

            $this->getEventManager()->triggerEvent(
                $this->createPasswordResetEvent(
                    PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_ERROR,
                    $user, $data, $result
                )
            );
        }

        return $result;
    }

    /**
     * @param $email
     * @param $token
     * @param $newPassword
     * @return UserOperationResult
     */
    public function resetPassword($email, $token, $newPassword)
    {
        $result = new UserOperationResult(true, $this->options->getMessagesOptions()
            ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_SUCCESS));

        $user = null;

        if (empty($email) || empty($token)) {
            $result = $this->createUserOperationResultWithMessages($this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_MISSING_PARAMS));

        } else {
            try {
                /** @var UserEntityInterface $user */
                $user = $this->find(['email' => $email]);
                if (!$user) {
                    $result = $this->createUserOperationResultWithMessages($this->options->getMessagesOptions()
                        ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_INVALID_EMAIL));
                } else {
                    $r = $this->mapper->findResetToken((int)$user->getId(), $token);
                    if ($r) {
                        $expireAt = $r['expireAt'];

                        if ($expireAt >= time()) {
                            $user->setPassword($this->passwordService->create($newPassword));

                            $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                                PasswordResetEvent::EVENT_PASSWORD_RESET_PRE,
                                $user
                            ));

                            $this->save($user);

                            $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                                PasswordResetEvent::EVENT_PASSWORD_RESET_POST,
                                $user
                            ));
                        } else {
                            $result = $this->createUserOperationResultWithMessages(
                                $this->options->getMessagesOptions()
                                    ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED));
                        }
                    } else {
                        $result = $this->createUserOperationResultWithMessages(
                            $this->options->getMessagesOptions()
                                ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_INVALID_TOKEN));
                    }
                }
            } catch (\Exception $e) {
                error_log("Password reset error: " . $e->getMessage());

                $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                    ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_ERROR);
                $result = $this->createUserOperationResultWithException($e, $message, $user);

                $this->getEventManager()->triggerEvent(
                    $this->createPasswordResetEvent(
                        PasswordResetEvent::EVENT_PASSWORD_RESET_ERROR,
                        $user, null, $result
                    )
                );
            }
        }

        return $result;
    }

    /**
     * @param $oldPassword
     * @param $newPassword
     * @return UserOperationResult
     */
    public function changePassword($oldPassword, $newPassword)
    {
        $result = new UserOperationResult(true, $this->options->getMessagesOptions()
            ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_OK));

        $identity = $this->authentication->getIdentity();
        //we always get it from DB, just to make sure hashed password is not missing
        $currentUser = $this->find([$this->getMapper()->getIdentifierName() => $identity->getId()]);

        if (!$currentUser) {
            return $this->createUserOperationResultWithMessages(
                $this->options->getMessagesOptions()
                    ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_INVALID_USER));
        }

        try {
            if ($this->passwordService->verify($oldPassword, $currentUser->getPassword())) {
                //update password
                $currentUser->setPassword($this->passwordService->create($newPassword));

                $this->getEventManager()->triggerEvent($this->createChangePasswordEvent(
                    ChangePasswordEvent::EVENT_CHANGE_PASSWORD_PRE, $currentUser
                ));

                $this->save($currentUser);

                $this->getEventManager()->triggerEvent($this->createChangePasswordEvent(
                    ChangePasswordEvent::EVENT_CHANGE_PASSWORD_POST, $currentUser
                ));
            } else {
                $result = $this->createUserOperationResultWithMessages(
                    $this->options->getMessagesOptions()
                        ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_INVALID_CURRENT_PASSWORD));
            }
        } catch (\Exception $e) {
            error_log("Change password error: " . $e->getMessage());

            $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_ERROR);
            $result = $this->createUserOperationResultWithException($e, $message, $currentUser);

            $this->getEventManager()->triggerEvent($this->createChangePasswordEvent(
                ChangePasswordEvent::EVENT_CHANGE_PASSWORD_ERROR,
                $currentUser, $result
            ));
        }

        return $result;
    }


    /**
     * Store a new user into the db, after it validates the data
     * trigger register events
     *
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function register(UserEntityInterface $user)
    {
        $result = new UserOperationResult(true, $this->options->getMessagesOptions()
            ->getMessage(MessagesOptions::MESSAGE_REGISTER_SUCCESS));

        $isAtomic = $this->isAtomicOperations();

        try {
            $this->setAtomicOperations(false);
            $this->mapper->beginTransaction();

            $user->setPassword($this->passwordService->create($user->getPassword()));
            if ($this->options->isEnableUserStatus()) {
                $user->setStatus($this->options->getRegisterOptions()->getDefaultUserStatus());
            }

            //trigger pre register event
            $this->getEventManager()->triggerEvent(
                $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_PRE, $user));

            $this->save($user);

            //get newly created user id and save it to the object
            $id = $this->mapper->lastInsertValue();
            if ($id) {
                $user->setId($id);
            }

            $result->setUser($user);

            //generate a confirm token if enabled and also trigger events
            if ($this->options->getConfirmAccountOptions()->isEnableAccountConfirmation()) {
                $this->generateConfirmToken($user);
            }

            //trigger post register event
            $this->getEventManager()->triggerEvent(
                $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_POST, $user));

            $this->mapper->commit();
            $this->setAtomicOperations($isAtomic);

        } catch (\Exception $e) {
            error_log("Register error: " . $e->getMessage());

            $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REGISTER_ERROR);
            $result = $this->createUserOperationResultWithException($e, $message, $user);

            //trigger error event
            $this->getEventManager()->triggerEvent(
                $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_ERROR, $user, $result));

            $this->mapper->rollback();
            $this->setAtomicOperations($isAtomic);
        }

        return $result;
    }

    /**
     * @param UserEntityInterface $user
     * @throws \Exception
     */
    protected function generateConfirmToken(UserEntityInterface $user)
    {
        $data = null;

        try {
            $data = new \stdClass();
            $data->userId = $user->getId();
            $data->token = md5(Rand::getString(32) . time() . $user->getEmail());

            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent(
                    ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_TOKEN_PRE,
                    $user,
                    $data
                ));

            $this->mapper->saveConfirmToken((array)$data);

            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent(
                    ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_TOKEN_POST,
                    $user,
                    $data
                ));

        } catch (\Exception $e) {
            error_log("Confirm token generation error: " . $e->getMessage());

            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent(
                    ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_TOKEN_ERROR,
                    $user,
                    $data
                ));

            throw $e;
        }
    }

    /**
     * @param UserEntityInterface $user
     * @return UserOperationResult
     */
    public function updateAccount(UserEntityInterface $user)
    {
        $result = new UserOperationResult(true, $this->options->getMessagesOptions()
            ->getMessage(MessagesOptions::MESSAGE_ACCOUNT_UPDATE_OK));

        $isAtomic = $this->isAtomicOperations();
        try {
            $this->setAtomicOperations(false);
            $this->mapper->beginTransaction();

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_PRE, $user));

            if(!empty($user->getPassword())) {
                $user->setPassword($this->passwordService->create($user->getPassword()));
            }
            $this->save($user);

            $result->setUser($user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_POST, $user));

            $this->mapper->commit();
            $this->setAtomicOperations($isAtomic);

        } catch (\Exception $e) {
            error_log('Update user error: ' . $e->getMessage());

            $message = $this->debug ? $e->getMessage() : $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_ACCOUNT_UPDATE_ERROR);

            $result = $this->createUserOperationResultWithException(
                $e, $message, $user);

            $this->getEventManager()->triggerEvent(
                $this->createUpdateEvent(UserUpdateEvent::EVENT_UPDATE_ERROR, $user, $result));

            $this->mapper->rollback();
            $this->setAtomicOperations($isAtomic);
        }

        return $result;
    }

    /**
     * @return PasswordInterface
     */
    public function getPasswordService()
    {
        return $this->passwordService;
    }

    /**
     * @param PasswordInterface $passwordService
     * @return UserService
     */
    public function setPasswordService($passwordService)
    {
        $this->passwordService = $passwordService;
        return $this;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getServerRequest()
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     * @return UserService
     */
    public function setServerRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return UserService
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param boolean $debug
     * @return UserService
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }


    /**
     * @param $messages
     * @param UserEntityInterface|null $user
     * @return UserOperationResult
     */
    protected function createUserOperationResultWithMessages($messages, UserEntityInterface $user = null)
    {
        $result = new UserOperationResult(false, $messages);
        if ($user) {
            $result->setUser($user);
        }

        return $result;
    }

    /**
     * @param \Exception $e
     * @param null $messages
     * @param UserEntityInterface|null $user
     * @return UserOperationResult
     */
    protected function createUserOperationResultWithException(
        \Exception $e,
        $messages = null,
        UserEntityInterface $user = null
    ) {
        if ($this->isDebug()) {
            $result = new UserOperationResult(false, $e->getMessage(), $e);
        } else {
            if ($messages) {
                $result = new UserOperationResult(false, $messages, $e);
            } else {
                $result = new UserOperationResult(false, $e->getMessage(), $e);
            }
        }

        if ($user) {
            $result->setUser($user);
        }

        return $result;
    }

    /**
     * @param Event $event
     * @return Event
     */
    protected function setupEventPsr7Messages(Event $event)
    {
        if ($this->request) {
            $event->setRequest($this->request);
        }
        if ($this->response) {
            $event->setResponse($this->response);
        }

        return $event;
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param ResultInterface|null $result
     * @param mixed $data
     * @return ConfirmAccountEvent
     */
    protected function createConfirmAccountEvent(
        $name = ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE,
        UserEntityInterface $user = null,
        $data = null,
        ResultInterface $result = null
    ) {
        $event = new ConfirmAccountEvent($this, $name, $user, $result);
        if ($data) {
            $event->setData($data);
        }

        return $this->setupEventPsr7Messages($event);
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param ResultInterface|null $result
     * @return RegisterEvent
     */
    protected function createRegisterEvent(
        $name = RegisterEvent::EVENT_REGISTER_PRE,
        UserEntityInterface $user = null,
        ResultInterface $result = null
    ) {
        $event = new RegisterEvent($this, $name, $user, $result);
        return $this->setupEventPsr7Messages($event);
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param mixed $data
     * @param ResultInterface|null $result
     * @return PasswordResetEvent
     */
    protected function createPasswordResetEvent(
        $name = PasswordResetEvent::EVENT_PASSWORD_RESET_PRE,
        UserEntityInterface $user = null,
        $data = null,
        ResultInterface $result = null
    ) {
        $event = new PasswordResetEvent($this, $name, $user, $result);
        if ($data) {
            $event->setData($data);
        }

        return $this->setupEventPsr7Messages($event);
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param null $data
     * @param ResultInterface|null $result
     * @return RememberTokenEvent
     */
    protected function createRememberTokenEvent(
        $name = RememberTokenEvent::EVENT_TOKEN_GENERATE_PRE,
        UserEntityInterface $user = null,
        $data = null,
        ResultInterface $result = null
    ) {
        $event = new RememberTokenEvent($this, $name, $user, $result);
        if ($data) {
            $event->setData($data);
        }

        return $this->setupEventPsr7Messages($event);
    }

    /**
     * @param string $name
     * @param UserEntityInterface|null $user
     * @param ResultInterface|null $result
     * @return Event
     */
    protected function createChangePasswordEvent(
        $name = ChangePasswordEvent::EVENT_CHANGE_PASSWORD_PRE,
        UserEntityInterface $user = null,
        ResultInterface $result = null
    ) {
        $event = new ChangePasswordEvent($this, $name, $user, $result);
        return $this->setupEventPsr7Messages($event);
    }

    /**
     * @param $name
     * @param UserEntityInterface|null $user
     * @param ResultInterface|null $result
     * @return Event
     */
    protected function createUpdateEvent(
        $name = UserUpdateEvent::EVENT_UPDATE_PRE,
        UserEntityInterface $user = null,
        ResultInterface $result = null
    ) {
        $event = new UserUpdateEvent($this, $name, $user, $result);
        return $this->setupEventPsr7Messages($event);
    }
}