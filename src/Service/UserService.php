<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 2/5/2017
 * Time: 5:16 AM
 */

declare(strict_types = 1);

namespace Dot\User\Service;

use Dot\Ems\Mapper\MapperManagerAwareInterface;
use Dot\Ems\Mapper\MapperManagerAwareTrait;
use Dot\User\Entity\UserEntity;
use Dot\User\Event\DispatchUserEventsTrait;
use Dot\User\Event\TokenEventListenerInterface;
use Dot\User\Event\TokenEventListenerTrait;
use Dot\User\Event\UserEvent;
use Dot\User\Event\UserEventListenerInterface;
use Dot\User\Event\UserEventListenerTrait;
use Dot\User\Exception\InvalidArgumentException;
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
    TokenEventListenerInterface
{
    use MapperManagerAwareTrait;
    use DispatchUserEventsTrait;
    use UserEventListenerTrait;
    use TokenEventListenerTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /** @var  TokenServiceInterface */
    protected $tokenService;

    /** @var  PasswordInterface */
    protected $passwordService;

    /**
     * UserService constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['user_options']) && $options['user_options'] instanceof UserOptions) {
            $this->userOptions = $options['user_options'];
        }

        if (isset($options['token_service']) && $options['token_service'] instanceof TokenServiceInterface) {
            $this->tokenService = $options['token_service'];
        }

        if (isset($options['password_service']) && $options['password_service'] instanceof PasswordInterface) {
            $this->passwordService = $options['password_service'];
        }

        if (isset($options['event_manager']) && $options['event_manager'] instanceof EventManagerInterface) {
            $this->setEventManager($options['event_manager']);
        }

        if (!$this->userOptions instanceof UserOptions) {
            throw new InvalidArgumentException('UserOptions is required and was not set');
        }

        if (!$this->tokenService instanceof TokenService) {
            throw new InvalidArgumentException('Token service is required and was not set');
        }

        if (!$this->passwordService instanceof PasswordInterface) {
            throw new InvalidArgumentException('Password service is required and was not set');
        }

        $this->attach($this->getEventManager(), 1000);
        $this->attach($this->tokenService->getEventManager(), 1000);
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
                'user' => $user, 'mapper' => $mapper, 'error' => $e
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
            $user = $mapper->getByEmail($email);
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
                    $r = $mapper->save($user);
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
                return new Result(['user' => $user, 'mapper' => $mapper], $this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::CONFIRM_ACCOUNT_INVALID_TOKEN));
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
            $user = $mapper->getByEmail($email);
            if ($user) {
                $token = $this->tokenService->findResetToken($user, $token);
                if ($token) {
                    //check validity
                    if ($token->getExpire() >= time()) {
                        $user->setPassword($this->passwordService->create($newPassword));

                        $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_PASSWORD_RESET, [
                            'user' => $user,
                            'token' => $token,
                            'mapper' => $mapper
                        ]);
                        if ($event->stopped()) {
                            $event->last();
                        }

                        $r = $mapper->save($user);
                        if ($r) {
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
                $user->setPassword($this->passwordService->create($newPassword));

                $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_ACCOUNT_UPDATE, [
                    'user' => $user,
                    'currentPassword' => $currentPassword,
                    'newPassword' => $newPassword,
                    'mapper' => $mapper,
                ]);
                if ($event->stopped()) {
                    return $event->last();
                }

                $r = $mapper->save($user);
                if ($r) {
                    $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_ACCOUNT_UPDATE, [
                        'user' => $user,
                        'currentPassword' => $currentPassword,
                        'newPassword' => $newPassword,
                        'mapper' => $mapper
                    ]);
                    return new Result(['user' => $user, 'mapper' => $mapper]);
                }
                $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_UPDATE_ERROR, [
                    'user' => $user,
                    'mapper' => $mapper,
                    'currentPassword' => $currentPassword,
                    'newPassword' => $newPassword,
                    'error' => ErrorCode::USER_SAVE_ERROR
                ]);
                return new Result(
                    ['user' => $user, 'mapper' => $mapper],
                    $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::CHANGE_PASSWORD_ERROR)
                );
            }
            $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_UPDATE_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'currentPassword' => $currentPassword,
                'newPassword' => $newPassword,
                'error' => ErrorCode::USER_PASSWORD_INVALID
            ]);
            return new Result(
                ['user' => $user, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::CURRENT_PASSWORD_INVALID)
            );
        } catch (\Exception $e) {
            $this->dispatchEvent(UserEvent::EVENT_USER_ACCOUNT_UPDATE_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'currentPassword' => $currentPassword,
                'newPassword' => $newPassword,
                'error' => $e
            ]);
            return new Result(['user' => $user, 'mapper' => $mapper], $e);
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
            $mapper->beginTransaction();

            $user->setPassword($this->passwordService->create($user->getPassword()));
            if ($this->userOptions->isEnableUserStatus()) {
                $user->setStatus($this->userOptions->getRegisterOptions()->getDefaultUserStatus());
            }
            $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_REGISTRATION, [
                'user' => $user,
                'mapper' => $mapper,
            ]);
            if ($event->stopped()) {
                return $event->last();
            }

            $r = $mapper->save($user);
            if ($r) {
                if ($this->userOptions->isEnableAccountConfirmation()) {
                    $t = $this->tokenService->generateConfirmToken($user);
                    if ($t->isValid()) {
                        // everything ok, commit transaction
                        $mapper->commit();
                        $this->dispatchEvent(UserEvent::EVENT_USER_AFTER_REGISTRATION, [
                            'user' => $user,
                            'mapper' => $mapper,
                            'confirmToken' => $t->getParam('token')
                        ]);
                        return new Result(
                            ['user' => $user, 'confirmToken' => $t->getParam('token'), 'mapper' => $mapper]
                        );
                    }
                    // here is fail confirm token create
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
            $mapper->rollback();
            $this->dispatchEvent(UserEvent::EVENT_USER_REGISTRATION_ERROR, [
                'user' => $user,
                'mapper' => $mapper,
                'error' => ErrorCode::USER_SAVE_ERROR
            ]);
            return new Result(['user' => $user, 'mapper' => $mapper], $this->userOptions->getMessagesOptions()
                ->getMessage(MessagesOptions::USER_REGISTER_ERROR));
        } catch (\Exception $e) {
            $mapper->rollback();
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
     * @return Result
     */
    public function updateAccount(UserEntity $user): Result
    {
        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());

        try {
            $user->setPassword($this->passwordService->create($user->getPassword()));

            $event = $this->dispatchEvent(UserEvent::EVENT_USER_BEFORE_ACCOUNT_UPDATE, [
                'user' => $user,
                'mapper' => $mapper,
            ]);
            if ($event->stopped()) {
                return $event->last();
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
    public function generateResetToken(array $data): Result
    {
        $email = $data['email'] ?? '';

        /** @var UserMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getUserEntity());
        $user = $mapper->getByEmail($email);
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
}
