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
use Dot\Event\Event;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Event\ChangePasswordEvent;
use Dot\User\Event\ConfirmAccountEvent;
use Dot\User\Event\Listener\UserListenerAwareInterface;
use Dot\User\Event\Listener\UserListenerAwareTrait;
use Dot\User\Event\PasswordResetEvent;
use Dot\User\Event\RegisterEvent;
use Dot\User\Event\RememberTokenEvent;
use Dot\User\Mapper\UserMapperInterface;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\ResultInterface;
use Dot\User\Result\UserOperationResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Hydrator\HydratorInterface;
use Zend\Math\Rand;

/**
 * Class UserService
 * @package Dot\User\Service
 */
class UserService implements UserServiceInterface, UserListenerAwareInterface
{
    use UserListenerAwareTrait;

    /** @var  UserMapperInterface */
    protected $userMapper;

    /** @var  UserOptions */
    protected $options;

    /** @var  UserEntityInterface */
    protected $userEntityPrototype;

    /** @var  HydratorInterface */
    protected $userEntityHydrator;

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
     * @param UserMapperInterface $userMapper
     * @param UserOptions $options
     * @param PasswordInterface $passwordService
     * @param AuthenticationInterface $authentication
     */
    public function __construct(
        UserMapperInterface $userMapper,
        UserOptions $options,
        PasswordInterface $passwordService,
        AuthenticationInterface $authentication
    )
    {
        $this->userMapper = $userMapper;
        $this->options = $options;
        $this->passwordService = $passwordService;
        $this->authentication = $authentication;
    }

    /**
     * Find user by its id
     *
     * @param $id
     * @return mixed
     */
    public function findUser($id)
    {
        return $this->userMapper->findUser($id);
    }

    /**
     * Get a user entity by some given field and value
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findUserBy($field, $value)
    {
        return $this->userMapper->findUserBy($field, $value);
    }

    /**
     * Save user is working as in create/update user, based on the presence of user id in the data
     *
     * @param $user
     * @return mixed
     */
    public function saveUser(UserEntityInterface $user)
    {
        if(!$user->getId()) {
            $this->userMapper->createUser($user);
        }
        else {
            $this->userMapper->updateUser($user);
        }
    }

    /**
     * Remove an user based on its id
     *
     * @param $id
     * @return mixed
     */
    public function removeUser($id)
    {
        return $this->userMapper->removeUser($id);
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

        try{
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
            $dbData = (array) $data;
            $dbData['token'] = md5($dbData['token']);

            $this->userMapper->saveRememberToken($dbData);

            $cookieData = base64_encode(serialize(['selector' => $selector, 'token' => $token]));

            $name = $this->options->getLoginOptions()->getRememberMeCookieName();
            $expire = $this->options->getLoginOptions()->getRememberMeCookieExpire();
            $secure = $this->options->getLoginOptions()->isRememberMeCookieSecure();

            setcookie($name, $cookieData, time() + $expire, "/", "", $secure, true);

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_GENERATE_POST,
                $user, $data
            ));
        }
        catch (\Exception $e) {
            error_log("Remember token generate error: " . $e->getMessage());
            $result = $this->createUserOperationResultWithException($e, $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REMEMBER_TOKEN_GENERATE_ERROR), $user);

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
        try{
            $r = $this->userMapper->findRememberToken($selector);
            if($r) {
                if($r['token'] == md5($token)) {
                    return $r;
                }
                else {
                    //clear any tokens for this user as security measure
                    $user = $this->findUser($r['userId']);
                    if($user) {
                        $this->removeRememberToken($user);
                    }
                }
            }
        }
        catch(\Exception $e) {
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
        try{
            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_PRE,
                $user
            ));

            $this->userMapper->removeRememberToken($user->getId());

            //clear cookies
            if(isset($_COOKIE[$this->options->getLoginOptions()->getRememberMeCookieName()])) {
                unset($_COOKIE[$this->options->getLoginOptions()->getRememberMeCookieName()]);
                setcookie($this->options->getLoginOptions()->getRememberMeCookieName(), '', time() - 3600, '/');
            }

            $this->getEventManager()->triggerEvent($this->createRememberTokenEvent(
                RememberTokenEvent::EVENT_TOKEN_REMOVE_POST,
                $user
            ));
        }
        catch(\Exception $e) {
            error_log("Remove remember token error for user " . $user->getId() . " with message: " . $e->getMessage());
            $result = $this->createUserOperationResultWithException($e, $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REMEMBER_TOKEN_REMOVE_ERROR), $user);
            
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
                $user = $this->findUserBy('email', $email);
                if ($user) {
                    $r = $this->userMapper->findConfirmToken($user->getId(), $token);
                    if ($r) {
                        $this->userMapper->beginTransaction();

                        //trigger pre event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_PRE, $user));

                        $user->setStatus($this->options->getConfirmAccountOptions()->getActiveUserStatus());
                        $this->saveUser($user);

                        $this->userMapper->removeConfirmToken($user->getId(), $token);

                        $this->userMapper->commit();

                        //post confirm event
                        $this->getEventManager()->triggerEvent(
                            $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_POST, $user));
                    }
                    else {
                        $result = $this->createUserOperationResultWithMessages(
                            $this->options->getMessagesOptions()
                                ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_TOKEN)
                        );
                    }
                }
                else {
                    $result = $this->createUserOperationResultWithMessages(
                        $this->options->getMessagesOptions()
                            ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_INVALID_EMAIL)
                    );
                }
            }
        }
        catch (\Exception $e) {
            error_log("Confirm account error: " . $e->getMessage(), E_USER_ERROR);
            $result = $this->createUserOperationResultWithException($e, $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_CONFIRM_ACCOUNT_ERROR),
                $user);

            //trigger error event
            $this->getEventManager()->triggerEvent(
                $this->createConfirmAccountEvent(ConfirmAccountEvent::EVENT_CONFIRM_ACCOUNT_ERROR, $user, $result));

            $this->userMapper->rollback();
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
            $user = $this->findUserBy('email', $email);

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

                $this->userMapper->saveResetToken((array)$data);

                $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                    PasswordResetEvent::EVENT_PASSWORD_RESET_TOKEN_POST,
                    $user,
                    $data
                ));
            }
        } catch (\Exception $e) {
            error_log("Password reset request error: " . $e->getMessage());
            $result = $this->createUserOperationResultWithException($e, $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_FORGOT_PASSWORD_ERROR), $user);

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

        }
        else {
            try {
                /** @var UserEntityInterface $user */
                $user = $this->userMapper->findUserBy('email', $email);
                if (!$user) {
                    $result = $this->createUserOperationResultWithMessages($this->options->getMessagesOptions()
                        ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_INVALID_EMAIL));
                }
                else {
                    $r = $this->userMapper->findResetToken((int) $user->getId(), $token);
                    if ($r) {
                        $expireAt = $r['expireAt'];

                        if ($expireAt >= time()) {
                            $user->setPassword($this->passwordService->create($newPassword));

                            $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                                PasswordResetEvent::EVENT_PASSWORD_RESET_PRE,
                                $user
                            ));

                            $this->saveUser($user);

                            $this->getEventManager()->triggerEvent($this->createPasswordResetEvent(
                                PasswordResetEvent::EVENT_PASSWORD_RESET_POST,
                                $user
                            ));
                        }
                        else {
                            $result = $this->createUserOperationResultWithMessages(
                                $this->options->getMessagesOptions()
                                    ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_TOKEN_EXPIRED));
                        }
                    }
                    else {
                        $result = $this->createUserOperationResultWithMessages(
                            $this->options->getMessagesOptions()
                                ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_INVALID_TOKEN));
                    }
                }
            }
            catch (\Exception $e) {
                error_log("Password reset error: " . $e->getMessage());
                $result = $this->createUserOperationResultWithException($e, $this->options->getMessagesOptions()
                    ->getMessage(MessagesOptions::MESSAGE_RESET_PASSWORD_ERROR), $user);

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
        $currentUser = $this->findUser($identity->getId());

        if(!$currentUser) {
            return $this->createUserOperationResultWithMessages(
                $this->options->getMessagesOptions()
                    ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_INVALID_USER));
        }

        try {
            if($this->passwordService->verify($currentUser->getPassword(), $oldPassword)) {
                //update password
                $currentUser->setPassword($this->passwordService->create($newPassword));

                $this->getEventManager()->triggerEvent($this->createUserUpdateEvent(
                    ChangePasswordEvent::EVENT_CHANGE_PASSWORD_PRE, $currentUser
                ));

                $this->saveUser($currentUser);

                $this->getEventManager()->triggerEvent($this->createUserUpdateEvent(
                    ChangePasswordEvent::EVENT_CHANGE_PASSWORD_POST, $currentUser
                ));
            }
            else {
                $result = $this->createUserOperationResultWithMessages(
                    $this->options->getMessagesOptions()
                        ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_INVALID_CURRENT_PASSWORD));
            }
        }
        catch (\Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            $result = $this->createUserOperationResultWithException(
                $e, $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_CHANGE_PASSWORD_ERROR), $currentUser);

            $this->getEventManager()->triggerEvent($this->createUserUpdateEvent(
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

        try {
            $this->userMapper->beginTransaction();

            $user->setPassword($this->passwordService->create($user->getPassword()));
            if ($this->options->isEnableUserStatus()) {
                $user->setStatus($this->options->getRegisterOptions()->getDefaultUserStatus());
            }

            //trigger pre register event
            $this->getEventManager()->triggerEvent(
                $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_PRE, $user));

            $this->saveUser($user);

            //get newly created user id and save it to the object
            $id = $this->userMapper->lastInsertValue();
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

            $this->userMapper->commit();
        }
        catch (\Exception $e) {
            error_log("Register error: " . $e->getMessage());
            $result = $this->createUserOperationResultWithException($e, $this->options->getMessagesOptions()
                ->getMessage(MessagesOptions::MESSAGE_REGISTER_ERROR), $user);

            //trigger error event
            $this->getEventManager()->triggerEvent(
                $this->createRegisterEvent(RegisterEvent::EVENT_REGISTER_ERROR, $user, $result));

            $this->userMapper->rollback();
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

            $this->userMapper->saveConfirmToken((array)$data);

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
     * @return UserMapperInterface
     */
    public function getUserMapper()
    {
        return $this->userMapper;
    }

    /**
     * @param UserMapperInterface $userMapper
     * @return UserService
     */
    public function setUserMapper(UserMapperInterface $userMapper)
    {
        $this->userMapper = $userMapper;
        return $this;
    }

    /**
     * @return UserOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param UserOptions $options
     * @return UserService
     */
    public function setOptions(UserOptions $options)
    {
        $this->options = $options;
        return $this;
    }


    /**
     * @return UserEntityInterface
     */
    public function getUserEntityPrototype()
    {
        return $this->userEntityPrototype;
    }

    /**
     * @param UserEntityInterface $userEntityPrototype
     * @return UserService
     */
    public function setUserEntityPrototype($userEntityPrototype)
    {
        $this->userEntityPrototype = $userEntityPrototype;
        return $this;
    }

    /**
     * @return HydratorInterface
     */
    public function getUserEntityHydrator()
    {
        return $this->userEntityHydrator;
    }

    /**
     * @param HydratorInterface $userEntityHydrator
     * @return UserService
     */
    public function setUserEntityHydrator($userEntityHydrator)
    {
        $this->userEntityHydrator = $userEntityHydrator;
        return $this;
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $request
     * @return UserService
     */
    public function setRequest(ServerRequestInterface $request)
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
        if($user) {
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
    protected function createUserOperationResultWithException(\Exception $e, $messages = null, UserEntityInterface $user = null)
    {
        if($this->isDebug()) {
            $result = new UserOperationResult(false, $e->getMessage(), $e);
        }
        else {
            if($messages) {
                $result = new UserOperationResult(false, $messages, $e);
            }
            else {
                $result = new UserOperationResult(false, $e->getMessage(), $e);
            }
        }

        if($user) {
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
        if($this->request) {
            $event->setRequest($this->request);
        }
        if($this->response) {
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
    )
    {
        $event = new ConfirmAccountEvent($this, $name, $user, $result);
        if($data) {
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
    )
    {
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
    )
    {
        $event = new PasswordResetEvent($this, $name, $user, $result);
        if($data) {
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
    )
    {
        $event = new RememberTokenEvent($this, $name, $user, $result);
        if($data) {
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
    protected function createUserUpdateEvent(
        $name = ChangePasswordEvent::EVENT_CHANGE_PASSWORD_PRE,
        UserEntityInterface $user = null,
        ResultInterface $result = null
    )
    {
        $event = new ChangePasswordEvent($this, $name, $user, $result);
        return $this->setupEventPsr7Messages($event);
    }
}