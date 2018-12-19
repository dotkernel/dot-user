<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Service;

use Dot\Doctrine\Mapper\EntityManagerAwareInterface;
use Dot\Doctrine\Mapper\EntityManagerAwareTrait;
use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Dot\User\Entity\ResetTokenEntity;
use Dot\User\Entity\UserEntity;
use Dot\User\Event\DispatchUserEventsTrait;
use Dot\User\Event\TokenEventListenerInterface;
use Dot\User\Event\TokenEventListenerTrait;
use Dot\User\Event\UserEvent;
use Dot\User\Event\UserEventListenerInterface;
use Dot\User\Event\UserEventListenerTrait;
use Dot\User\Mapper\UserMapperInterface;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\ErrorCode;
use Dot\User\Result\Result;
use Zend\Crypt\Password\PasswordInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UserService
 * @package Dot\User\Service
 */
class UserService implements
    UserServiceInterface,
    MapperManagerAwareInterface,
    UserEventListenerInterface,
    TokenEventListenerInterface,
    EntityManagerAwareInterface
{
    use MapperManagerAwareTrait;
    use DispatchUserEventsTrait;
    use EntityManagerAwareTrait;
    use UserEventListenerTrait,
        TokenEventListenerTrait {
        UserEventListenerTrait::attach as userEventAttach;
        TokenEventListenerTrait::attach as tokenEventAttach;
        UserEventListenerTrait::detach as userEventDetach;
        TokenEventListenerTrait::detach as tokenEventDetach;
    }

    /** @var  UserOptions */
    protected $userOptions;

    /** @var  TokenServiceInterface */
    protected $tokenService;

    /** @var  PasswordInterface */
    protected $passwordService;

    /**
     * UserService constructor.
     * @param TokenServiceInterface $tokenService
     * @param PasswordInterface $passwordService
     * @param UserOptions $userOptions
     */
    public function __construct(
        TokenServiceInterface $tokenService,
        PasswordInterface $passwordService,
        UserOptions $userOptions
    ) {
        $this->tokenService = $tokenService;
        $this->userOptions = $userOptions;
        $this->passwordService = $passwordService;
    }

    /**
     * @param $id
     * @param array $options
     * @return UserEntity|null
     */
    public function find($id, array $options = []): ?UserEntity
    {
        /** @var UserMapperInterface $mapper */

        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        return $mapper->get($id, $options);
    }

    /**
     * @param string $email
     * @param array $options
     * @return UserEntity|null
     */
    public function findByEmail(string $email, array $options = []): ?UserEntity
    {
        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        return $mapper->getByEmail($email, $options);
    }

    /**
     * @param UserEntity $user
     * @return mixed
     */
    public function delete(UserEntity $user): Result
    {
        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());

        try {
            $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_DELETE, [
                'user' => $user,
                'mapper' => $mapper
            ]);
            if ($event->stopped()) {
                return $event->last();
            }

            $success = $mapper->delete($user);
            if ($success) {
                $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_DELETE, ['user' => $user, 'mapper' => $mapper]);
                return new Result(['user' => $user, 'mapper' => $mapper]);
            }

            $this->dispatchEvent(UserEvent::EVENT_USER_DELETE_ERROR, ['user' => $user, 'mapper' => $mapper]);
            return new Result(
                ['user' => $user, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::USER_DELETE_ERROR)
            );
        } catch (\Exception $e) {
            $this->dispatchEvent(UserEvent::EVENT_USER_DELETE_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'error' => $e
            ]);
            return new Result(['user' => $user, 'mapper' => $mapper], $e);
        }
    }

    /**
     * @param array $params
     * @return Result
     */
    public function confirmAccount(array $params): Result
    {
        $email = $params['email'] ?? '';
        $token = $params['token'] ?? '';

        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        $user = null;
        try {
            $user = $mapper->getByEmail($email, ['conditions' => ['status' => UserEntity::STATUS_PENDING]]);
            if ($user instanceof UserEntity) {
                $token = $this->tokenService->findConfirmToken($user, $token);
                if ($token) {
                    $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_ACCOUNT_CONFIRMATION, [
                        'user' => $user,
                        'token' => $token,
                        'mapper' => $mapper
                    ]);
                    if ($event->stopped()) {
                        return $event->last();
                    }

                    $mapper->beginTransaction();

                    $user->setStatus($this->userOptions->getConfirmedAccountStatus());
                    $r = $mapper->save($user, ['atomic' => false]);
                    if ($r) {
                        $this->tokenService->deleteConfirmTokens($user);

                        $mapper->commit();
                        $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_ACCOUNT_CONFIRMATION, [
                            'user' => $user,
                            'token' => $token,
                            'mapper' => $mapper
                        ]);

                        return new Result(['token' => $token, 'user' => $user, 'mapper' => $mapper]);
                    } else {
                        $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_CONFIRMATION_ERROR, [
                            'user' => $user,
                            'token' => $token,
                            'mapper' => $mapper,
                            'error' => ErrorCode::USER_SAVE_ERROR
                        ]);
                        $mapper->rollback();
                        return new Result(
                            ['token' => $token, 'user' => $user, 'mapper' => $mapper],
                            $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_ERROR)
                        );
                    }
                }
                $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_CONFIRMATION_ERROR, [
                    'user' => $user,
                    'token' => $token,
                    'mapper' => $mapper,
                    'error' => ErrorCode::TOKEN_NOT_FOUND
                ]);
                return new Result(
                    ['user' => $user, 'mapper' => $mapper, 'token' => $token],
                    $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_INVALID_TOKEN)
                );
            }
            $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_CONFIRMATION_ERROR, [
                'email' => $email,
                'mapper' => $mapper,
                'error' => ErrorCode::USER_NOT_FOUND
            ]);
            return new Result(
                ['email' => $email, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_INVALID_EMAIL)
            );
        } catch (\Exception $e) {
            $errorData = ['mapper' => $mapper];
            if (isset($user)) {
                $errorData['user'] = $user;
            }
            if (isset($token)) {
                $errorData['token'] = $token;
            }
            $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_CONFIRMATION_ERROR, $errorData + ['error' => $e]);
            $mapper->rollback();
            $result = new Result($errorData, $e);

            return $result;
        }
    }

    public function optOut(array $params): Result
    {
        $email = $params['email'] ?? '';
        $token = $params['token'] ?? '';

        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        try {
            $user = $mapper->getByEmail($email, ['conditions' => ['status' => UserEntity::STATUS_PENDING]]);
            if ($user instanceof UserEntity) {
                $token = $this->tokenService->findConfirmToken($user, $token);
                if ($token) {
                    $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_OPT_OUT, [
                        'user' => $user,
                        'token' => $token,
                        'mapper' => $mapper
                    ]);
                    if ($event->stopped()) {
                        return $event->last();
                    }

                    $mapper->beginTransaction();
                    $r = $mapper->delete($user, ['atomic' => false]);
                    if ($r) {
                        $this->tokenService->deleteConfirmTokens($user);

                        $mapper->commit();
                        $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_OPT_OUT, [
                            'user' => $user,
                            'token' => $token,
                            'mapper' => $mapper
                        ]);

                        return new Result(['token' => $token, 'user' => $user, 'mapper' => $mapper]);
                    } else {
                        $this->dispatchEvent(UserEvent::EVENT_USER_OPT_OUT_ERROR, [
                            'user' => $user,
                            'token' => $token,
                            'mapper' => $mapper,
                            'error' => ErrorCode::USER_DELETE_ERROR
                        ]);
                        $mapper->rollback();
                        return new Result(
                            ['token' => $token, 'user' => $user, 'mapper' => $mapper],
                            $this->userOptions->getMessagesOptions()
                                ->getMessage(MessagesOptions::OPT_OUT_ERROR)
                        );
                    }
                }
                $this->dispatchEvent(UserEvent::EVENT_USER_OPT_OUT_ERROR, [
                    'user' => $user,
                    'token' => $token,
                    'mapper' => $mapper,
                    'error' => ErrorCode::TOKEN_NOT_FOUND
                ]);
                return new Result(
                    ['user' => $user, 'mapper' => $mapper, 'token' => $token],
                    $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::OPT_OUT_INVALID_PARAMS)
                );
            }
            $this->dispatchEvent(UserEvent::EVENT_USER_OPT_OUT_ERROR, [
                'email' => $email,
                'mapper' => $mapper,
                'error' => ErrorCode::USER_NOT_FOUND
            ]);
            return new Result(
                ['email' => $email, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::OPT_OUT_INVALID_PARAMS)
            );
        } catch (\Exception $e) {
            $errorData = ['mapper' => $mapper];
            if (isset($user)) {
                $errorData['user'] = $user;
            }
            if (isset($token)) {
                $errorData['token'] = $token;
            }
            $this->dispatchEvent(UserEvent::EVENT_USER_OPT_OUT_ERROR, $errorData + ['error' => $e]);
            $mapper->rollback();
            $result = new Result($errorData, $e);

            return $result;
        }
    }

    /**
     * @param array $data
     * @return Result
     */
    public function resetPassword(array $data): Result
    {
        $email = $data['email'] ?? '';
        $token = $data['token'] ?? '';
        $newPassword = $data['user']['password'];

        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        $user = null;
        try {
            $user = $mapper->getByEmail(
                $email,
                ['conditions' => ['status' => $this->userOptions->getLoginOptions()->getAllowedStatus()]]
            );
            if ($user) {
                $token = $this->tokenService->findResetToken($user, $token);
                if ($token) {
                    //check validity
                    if ((int)$token->getExpire() >= time()) {
                        $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_PASSWORD_RESET, [
                            'user' => $user,
                            'token' => $token,
                            'mapper' => $mapper
                        ]);
                        if ($event->stopped()) {
                            return $event->last();
                        }

                        $user->setPassword($this->passwordService->create($newPassword));
                        $r = $mapper->save($user);
                        if ($r) {
                            // delete reset tokens for this user, as a security measure, if reset successful
                            $this->tokenService->deleteResetTokens($user);

                            $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_PASSWORD_RESET, [
                                'user' => $user,
                                'token' => $token,
                                'mapper' => $mapper
                            ]);
                            return new Result(['user' => $user, 'token' => $token, 'mapper' => $mapper]);
                        }

                        $this->dispatchEvent(UserEvent::EVENT_USER_RESET_PASSWORD_ERROR, [
                            'user' => $user,
                            'token' => $token,
                            'mapper' => $mapper,
                            'error' => ErrorCode::USER_SAVE_ERROR
                        ]);
                        return new Result(
                            ['user' => $user, 'token' => $token, 'mapper' => $mapper],
                            $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::RESET_PASSWORD_ERROR)
                        );
                    }
                    // delete the expired token
                    if ($token instanceof ResetTokenEntity && $token->getId()) {
                        $this->tokenService->deleteResetToken($token);
                    }

                    $this->dispatchEvent(UserEvent::EVENT_USER_RESET_PASSWORD_ERROR, [
                        'user' => $user,
                        'token' => $token,
                        'mapper' => $mapper,
                        'error' => ErrorCode::TOKEN_EXPIRED
                    ]);
                    return new Result(
                        ['user' => $user, 'token' => $token, 'mapper' => $mapper],
                        $this->userOptions->getMessagesOptions()
                            ->getMessage(MessagesOptions::RESET_TOKEN_EXPIRED)
                    );
                }

                $this->dispatchEvent(UserEvent::EVENT_USER_RESET_PASSWORD_ERROR, [
                    'user' => $user,
                    'token' => $token,
                    'mapper' => $mapper,
                    'error' => ErrorCode::TOKEN_NOT_FOUND
                ]);
                return new Result(
                    ['user' => $user, 'mapper' => $mapper, 'token' => $token],
                    $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::RESET_TOKEN_INVALID)
                );
            }

            $this->dispatchEvent(UserEvent::EVENT_USER_RESET_PASSWORD_ERROR, [
                'email' => $email,
                'mapper' => $mapper,
                'error' => ErrorCode::USER_NOT_FOUND
            ]);
            return new Result([], $this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::RESET_PASSWORD_INVALID_EMAIL));
        } catch (\Exception $e) {
            $errorData = ['mapper' => $mapper];
            if ($user) {
                $errorData['user'] = $user;
            }
            if ($token) {
                $errorData['token'] = $token;
            }
            $this->dispatchEvent(UserEvent::EVENT_USER_RESET_PASSWORD_ERROR, $errorData + ['error' => $e]);
            $result = new Result($errorData, $e);

            return $result;
        }
    }

    /**
     * @param UserEntity $user
     * @param array $data
     * @return Result
     */
    public function changePassword(UserEntity $user, array $data): Result
    {
        $currentPassword = $data['currentPassword'];
        $newPassword = $data['user']['password'];

        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());

        try {
            if ($this->passwordService->verify($currentPassword, $user->getPassword())) {
                $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_CHANGE_PASSWORD, [
                    'user' => $user,
                    'currentPassword' => $currentPassword,
                    'newPassword' => $newPassword,
                    'mapper' => $mapper,
                ]);
                if ($event->stopped()) {
                    return $event->last();
                }
                $user->setPassword($this->passwordService->create($newPassword));
                $r = $mapper->save($user);
                if ($r) {
                    // does not sign out the user, but at least deletes any remember tokens and cookies
                    // to force user to sign up after its session expires
                    $this->tokenService->deleteRememberTokens(['userId' => $user->getId()]);
                    $this->tokenService->clearRememberCookie();

                    $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_CHANGE_PASSWORD, [
                        'user' => $user,
                        'mapper' => $mapper
                    ]);
                    return new Result(['user' => $user, 'mapper' => $mapper]);
                }
                $this->dispatchEvent(UserEvent::EVENT_USER_CHANGE_PASSWORD_ERROR, [
                    'user' => $user,
                    'mapper' => $mapper,
                    'currentPassword' => $currentPassword,
                    'newPassword' => $newPassword,
                    'error' => ErrorCode::USER_SAVE_ERROR
                ]);
                return new Result(
                    [
                        'user' => $user,
                        'mapper' => $mapper,
                        'currentPassword' => $currentPassword,
                        'newPassword' => $newPassword
                    ],
                    $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::CHANGE_PASSWORD_ERROR)
                );
            }
            $this->dispatchEvent(UserEvent::EVENT_USER_CHANGE_PASSWORD_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'currentPassword' => $currentPassword,
                'newPassword' => $newPassword,
                'error' => ErrorCode::USER_PASSWORD_INVALID
            ]);
            return new Result(
                [
                    'user' => $user,
                    'mapper' => $mapper,
                    'currentPassword' => $currentPassword,
                    'newPassword' => $newPassword
                ],
                $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::CURRENT_PASSWORD_INVALID)
            );
        } catch (\Exception $e) {
            $this->dispatchEvent(UserEvent::EVENT_USER_CHANGE_PASSWORD_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'currentPassword' => $currentPassword,
                'newPassword' => $newPassword,
                'error' => $e
            ]);
            return new Result(
                [
                    'user' => $user,
                    'mapper' => $mapper,
                    'currentPassword' => $currentPassword,
                    'newPassword' => $newPassword
                ],
                $e
            );
        }
    }

    /**
     * @param UserEntity $user
     * @return Result
     */
    public function register(UserEntity $user): Result
    {
        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());

        try {
            $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_REGISTRATION, [
                'user' => $user,
                'mapper' => $mapper,
            ]);
            if ($event->stopped()) {
                return $event->last();
            }

            // this is more of a demo of manual transaction handling
            // if the user confirm token fails to be created, it will rollback the registration
            // this certainly could have been implemented in more than one way
            $mapper->beginTransaction();
            $user->setPassword($this->passwordService->create($user->getPassword()));
            $user->setStatus($this->userOptions->getRegisterOptions()->getDefaultUserStatus());

            $r = $mapper->save($user, ['atomic' => false]);
            if ($r) {
                if ($this->userOptions->isEnableAccountConfirmation()) {
                    $t = $this->tokenService->generateConfirmToken($user, ['atomic' => false]);
                    if ($t->isValid()) {
                        // everything ok, commit transaction
                        $mapper->commit();
                        $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_REGISTRATION, [
                            'user' => $user,
                            'mapper' => $mapper,
                            'token' => $t->getParam('token')
                        ]);
                        return new Result(
                            ['user' => $user, 'token' => $t->getParam('token'), 'mapper' => $mapper]
                        );
                    }
                    // if reach here, failed confirm token create
                    $mapper->rollback();
                    $this->dispatchEvent(UserEvent::EVENT_USER_REGISTRATION_ERROR, [
                        'user' => $user,
                        'mapper' => $mapper,
                        'error' => $t->getParam('error')
                    ]);
                    return new Result(['user' => $user, 'mapper' => $mapper], $this->userOptions->getMessagesOptions()
                        ->getMessage(MessagesOptions::CONFIRM_TOKEN_SAVE_ERROR));
                }
                // here is valid registration, without confirm token
                $mapper->commit();
                $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_REGISTRATION, [
                    'user' => $user,
                    'mapper' => $mapper,
                ]);
                return new Result(['user' => $user, 'mapper' => $mapper]);
            }
            // here is invalid user save
            if ($mapper->inTransaction()) {
                $mapper->rollback();
            }

            $this->dispatchEvent(UserEvent::EVENT_USER_REGISTRATION_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'error' => ErrorCode::USER_SAVE_ERROR
            ]);
            return new Result(['user' => $user, 'mapper' => $mapper], $this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::USER_REGISTER_ERROR));
        } catch (\Exception $e) {
            if ($mapper->inTransaction()) {
                $mapper->rollback();
            }

            $this->dispatchEvent(UserEvent::EVENT_USER_REGISTRATION_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'error' => $e
            ]);
            return new Result(['user' => $user, 'mapper' => $mapper], $e);
        }
    }

    /**
     * @param UserEntity $user
     * @param bool $hashPassword
     * @return Result
     */
    public function updateAccount(UserEntity $user, bool $hashPassword = false): Result
    {
        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());

        try {
            $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_ACCOUNT_UPDATE, [
                'user' => $user,
                'mapper' => $mapper,
            ]);
            if ($event->stopped()) {
                return $event->last();
            }

            if ($hashPassword) {
                $user->setPassword($this->passwordService->create($user->getPassword()));
            }

            $r = $mapper->save($user);
            if ($r) {
                $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_ACCOUNT_UPDATE, [
                    'user' => $user,
                    'mapper' => $mapper
                ]);
                return new Result(['user' => $user, 'mapper' => $mapper]);
            }

            $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_UPDATE_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'error' => ErrorCode::USER_SAVE_ERROR
            ]);
            return new Result(['user' => $user, 'mapper' => $mapper], $this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::USER_UPDATE_ERROR));
        } catch (\Exception $e) {
            $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_UPDATE_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'error' => $e
            ]);
            return new Result(['user' => $user, 'mapper' => $mapper], $e);
        }
    }

    /**
     * @param array $data
     * @return Result
     */
    public function resetPasswordRequest(array $data): Result
    {
        $email = $data['email'] ?? '';

        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        $user = $mapper->getByEmail($email, [
            'conditions' => [
                'status' => $this->userOptions->getLoginOptions()->getAllowedStatus()
            ]
        ]);

        if ($user) {
            return $this->tokenService->generateResetToken($user);
        }

        // if email is not a registered account, return a valid response, as if the request succeeded
        return new Result([]);
    }

    /**
     * @return UserEntity
     */
    public function newUser(): UserEntity
    {
        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        /** @var UserEntity $entity */
        $entity = $mapper->newEntity();
        return $entity;
    }

    /**
     * @return TokenServiceInterface
     */
    public function getTokenService(): TokenServiceInterface
    {
        return $this->tokenService;
    }

    /**
     * @param EventManagerInterface $events
     * @param int $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $identifiers = $events->getIdentifiers();
        if (in_array(UserService::class, $identifiers)) {
            $this->userEventAttach($events, $priority);
        }

        if (in_array(TokenService::class, $identifiers)) {
            $this->tokenEventAttach($events, $priority);
        }
    }

    /**
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        $identifiers = $events->getIdentifiers();
        if (in_array(UserService::class, $identifiers)) {
            $this->userEventDetach($events);
        }

        if (in_array(TokenService::class, $identifiers)) {
            $this->tokenEventDetach($events);
        }
    }
}
