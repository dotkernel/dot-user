<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/14/2017
 * Time: 12:11 AM
 */

declare(strict_types = 1);

namespace Dot\User\Service;

use Dot\Ems\Mapper\MapperManagerAwareInterface;
use Dot\Ems\Mapper\MapperManagerAwareTrait;
use Dot\User\Entity\AbstractTokenEntity;
use Dot\User\Entity\ConfirmTokenEntity;
use Dot\User\Entity\RememberTokenEntity;
use Dot\User\Entity\ResetTokenEntity;
use Dot\User\Entity\UserEntity;
use Dot\User\Event\DispatchTokenEventsTrait;
use Dot\User\Event\TokenEvent;
use Dot\User\Event\TokenEventListenerInterface;
use Dot\User\Event\TokenEventListenerTrait;
use Dot\User\Mapper\TokenMapperInterface;
use Dot\User\Options\MessagesOptions;
use Dot\User\Options\UserOptions;
use Dot\User\Result\ErrorCode;
use Dot\User\Result\Result;
use Zend\Math\Rand;

/**
 * Class TokenService
 * @package Dot\User\Service
 */
class TokenService implements
    TokenServiceInterface,
    MapperManagerAwareInterface,
    TokenEventListenerInterface
{
    use MapperManagerAwareTrait;
    use DispatchTokenEventsTrait;
    use TokenEventListenerTrait;

    /** @var  UserOptions */
    protected $userOptions;

    /**
     * TokenService constructor.
     * @param UserOptions $userOptions
     */
    public function __construct(UserOptions $userOptions)
    {
        $this->userOptions = $userOptions;
    }

    /**
     * @param UserEntity $user
     * @param string $token
     * @return ConfirmTokenEntity|null
     */
    public function findConfirmToken(UserEntity $user, string $token): ?ConfirmTokenEntity
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getConfirmTokenEntity());
        $token = $mapper->find(
            'all',
            [
                'conditions' => [
                    'userId' => $user->getId(),
                    'token' => $token,
                    'type' => AbstractTokenEntity::TOKEN_CONFIRM
                ]
            ]
        );

        if ($token && $token[0] instanceof ConfirmTokenEntity) {
            return $token[0];
        }

        return null;
    }

    /**
     * @param UserEntity $user
     * @return int
     */
    public function deleteConfirmTokens(UserEntity $user): int
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getConfirmTokenEntity());
        return $mapper->deleteAll(['userId' => $user->getId(), 'type' => AbstractTokenEntity::TOKEN_CONFIRM]);
    }

    /**
     * @param UserEntity $user
     * @return Result
     */
    public function generateConfirmToken(UserEntity $user): Result
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getConfirmTokenEntity());
        $token = null;
        try {
            /** @var ConfirmTokenEntity $token */
            $token = $mapper->newEntity();

            $token->setUserId($user->getId());
            $token->setToken(md5(Rand::getString(32) . time() . $user->getEmail()));

            $event = $this->dispatchEvent(TokenEvent::EVENT_TOKEN_BEFORE_SAVE_CONFIRM_TOKEN, [
                'token' => $token,
                'user' => $user,
                'mapper' => $mapper
            ]);
            if ($event->stopped()) {
                return $event->last();
            }

            $t = $mapper->save($token);
            if ($t) {
                $this->dispatchEvent(TokenEvent::EVENT_TOKEN_AFTER_SAVE_CONFIRM_TOKEN, [
                    'token' => $token,
                    'user' => $user,
                    'mapper' => $mapper
                ]);
                return new Result(['token' => $token, 'user' => $user, 'mapper' => $mapper]);
            }

            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_CONFIRM_TOKEN_SAVE_ERROR, [
                'token' => $token,
                'user' => $user,
                'mapper' => $mapper,
                'error' => ErrorCode::TOKEN_SAVE_ERROR
            ]);
            return new Result(
                ['token' => $token, 'user' => $user, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::CONFIRM_TOKEN_SAVE_ERROR)
            );
        } catch (\Exception $e) {
            $errorData = ['mapper' => $mapper, 'user' => $user];
            if ($token) {
                $errorData['token'] = $token;
            }
            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_CONFIRM_TOKEN_SAVE_ERROR, $errorData + ['error' => $e]);
            return new Result($errorData, $e);
        }
    }

    /**
     * @param UserEntity $user
     * @return Result
     */
    public function generateRememberToken(UserEntity $user): Result
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getRememberTokenEntity());
        /** @var RememberTokenEntity $token */
        $token = $mapper->newEntity();

        try {
            $options = $this->userOptions->getLoginOptions();

            $token->setSelector(Rand::getString(32));
            $token->setToken(Rand::getString(32));
            $token->setUserId($user->getId());
            $token->setExpire((string)(time() + $options->getRememberTokenExpire()));

            $event = $this->dispatchEvent(TokenEvent::EVENT_TOKEN_BEFORE_SAVE_REMEMBER_TOKEN, [
                'token' => $token,
                'user' => $user,
                'mapper' => $mapper
            ]);
            if ($event->stopped()) {
                return $event->last();
            }

            $tokenValue = $token->getToken();
            // hash the token value just before we save it
            $token->setToken(md5($token->getToken()));
            $r = $mapper->save($token);

            if ($r) {
                $token->setToken($tokenValue);
                $this->generateRememberCookie($token);

                $this->dispatchEvent(TokenEvent::EVENT_TOKEN_AFTER_SAVE_REMEMBER_TOKEN, [
                    'token' => $token,
                    'user' => $user,
                    'mapper' => $mapper
                ]);
                return new Result(['user' => $user, 'token' => $token, 'mapper' => $mapper]);
            }

            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_REMEMBER_TOKEN_SAVE_ERROR, [
                'token' => $token,
                'user' => $user,
                'mapper' => $mapper,
                'error' => ErrorCode::TOKEN_SAVE_ERROR
            ]);
            return new Result(
                ['user' => $user, 'token' => $token, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::REMEMBER_TOKEN_SAVE_ERROR)
            );
        } catch (\Exception $e) {
            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_REMEMBER_TOKEN_SAVE_ERROR, [
                'token' => $token,
                'user' => $user,
                'mapper' => $mapper,
                'error' => $e
            ]);
            return new Result(['user' => $user, 'token' => $token, 'mapper' => $mapper], $e);
        }
    }

    /**
     * @param RememberTokenEntity $token
     */
    public function generateRememberCookie(RememberTokenEntity $token)
    {
        $options = $this->userOptions->getLoginOptions();
        $cookieData = base64_encode(serialize(['selector' => $token->getSelector(), 'token' => $token->getToken()]));

        $name = $options->getRememberCookieName();
        $secure = $options->isRememberCookieSecure();

        setcookie($name, $cookieData, time() + $options->getRememberTokenExpire(), "/", "", $secure, true);
    }

    /**
     * @param string $selector
     * @param string $clearToken
     * @return Result
     */
    public function validateRememberToken(string $selector, string $clearToken): Result
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getRememberTokenEntity());
        $token = null;
        try {
            $token = $mapper->getBySelector(
                $selector,
                ['conditions' => ['type' => AbstractTokenEntity::TOKEN_REMEMBER]]
            );

            if ($token) {
                $event = $this->dispatchEvent(TokenEvent::EVENT_TOKEN_BEFORE_VALIDATE_REMEMBER_TOKEN, [
                    'token' => $token,
                    'selector' => $selector,
                    'userToken' => $clearToken,
                    'mapper' => $mapper
                ]);
                if ($event->stopped()) {
                    return $event->last();
                }

                if (hash_equals($token->getToken(), md5($clearToken))
                    && (int) $token->getExpire() >= time()) {
                    $this->dispatchEvent(TokenEvent::EVENT_TOKEN_AFTER_VALIDATE_REMEMBER_TOKEN, [
                        'token' => $token,
                        'userToken' => $clearToken,
                        'selector' => $selector,
                        'mapper' => $mapper
                    ]);
                    return new Result(
                        [
                            'token' => $token,
                            'userToken' => $clearToken,
                            'selector' => $selector,
                            'mapper' => $mapper
                        ]
                    );
                } else {
                    //clear any remember tokens as a security measure
                    $this->deleteRememberTokens(['userId' => $token->getUserId()]);

                    $this->dispatchEvent(TokenEvent::EVENT_TOKEN_REMEMBER_TOKEN_VALIDATION_ERROR, [
                        'token' => $token,
                        'userToken' => $clearToken,
                        'selector' => $selector,
                        'mapper' => $mapper,
                        'error' => ErrorCode::TOKEN_INVALID
                    ]);
                    return new Result(
                        ['token' => $token, 'userToken' => $clearToken, 'selector' => $selector, 'mapper' => $mapper],
                        $this->userOptions->getMessagesOptions()->getMessage(MessagesOptions::REMEMBER_TOKEN_INVALID)
                    );
                }
            }
            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_REMEMBER_TOKEN_VALIDATION_ERROR, [
                'userToken' => $clearToken,
                'selector' => $selector,
                'mapper' => $mapper,
                'error' => ErrorCode::TOKEN_NOT_FOUND
            ]);
            return new Result(
                ['selector' => $selector, 'userToken' => $clearToken, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::REMEMBER_TOKEN_INVALID)
            );
        } catch (\Exception $e) {
            $errorData = ['mapper' => $mapper, 'selector' => $selector, 'userToken' => $clearToken];
            if ($token) {
                $errorData['token'] = $token;
            }
            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_REMEMBER_TOKEN_VALIDATION_ERROR, $errorData + ['error' => $e]);
            return new Result($errorData, $e);
        }
    }

    /**
     * @param array $conditions
     * @return int
     */
    public function deleteRememberTokens(array $conditions): int
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getRememberTokenEntity());
        $n = $mapper->deleteAll(array_merge($conditions, ['type' => AbstractTokenEntity::TOKEN_REMEMBER]));

        //clear cookies
        $options = $this->userOptions->getLoginOptions();
        if (isset($_COOKIE[$options->getRememberCookieName()])) {
            unset($_COOKIE[$options->getRememberCookieName()]);
            setcookie($options->getRememberCookieName(), '', time() - 3600, '/');
        }

        return $n;
    }

    /**
     * @param UserEntity $user
     * @param string $token
     * @return ResetTokenEntity|null
     */
    public function findResetToken(UserEntity $user, string $token): ?ResetTokenEntity
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getResetTokenEntity());
        $token = $mapper->find(
            'all',
            [
                'conditions' => [
                    'userId' => $user->getId(),
                    'token' => $token,
                    'type' => AbstractTokenEntity::TOKEN_RESET
                ]
            ]
        );

        if ($token && isset($token[0])) {
            return $token[0];
        }

        return null;
    }

    /**
     * @param UserEntity $user
     * @return int
     */
    public function deleteResetTokens(UserEntity $user): int
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getResetTokenEntity());
        return $mapper->deleteAll(['userId' => $user->getId(), 'type' => AbstractTokenEntity::TOKEN_RESET]);
    }

    /**
     * @param ResetTokenEntity $token
     * @return mixed
     */
    public function deleteResetToken(ResetTokenEntity $token)
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getResetTokenEntity());
        return $mapper->delete($token);
    }

    /**
     * @param UserEntity $user
     * @return Result
     */
    public function generateResetToken(UserEntity $user): Result
    {
        /** @var TokenMapperInterface $mapper */
        $mapper = $this->getMapperManager()->get($this->userOptions->getResetTokenEntity());
        $token = null;
        try {
            /** @var ResetTokenEntity $token */
            $token = $mapper->newEntity();

            $token->setUserId($user->getId());
            $token->setToken(md5(Rand::getString(32) . time() . $user->getEmail()));
            $token->setExpire(time() + $this->userOptions->getPasswordRecoveryOptions()->getResetTokenTimeout());

            $event = $this->dispatchEvent(TokenEvent::EVENT_TOKEN_BEFORE_SAVE_RESET_TOKEN, [
                'token' => $token,
                'user' => $user,
                'mapper' => $mapper,
            ]);

            if ($event->stopped()) {
                return $event->last();
            }

            $t = $mapper->save($token);
            if ($t) {
                $this->dispatchEvent(TokenEvent::EVENT_TOKEN_AFTER_SAVE_RESET_TOKEN, [
                    'token' => $token,
                    'user' => $user,
                    'mapper' => $mapper,
                ]);
                return new Result(['token' => $token, 'user' => $user, 'mapper' => $mapper]);
            }
            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_RESET_TOKEN_SAVE_ERROR, [
                'token' => $token,
                'user' => $user,
                'mapper' => $mapper,
                'error' => ErrorCode::TOKEN_SAVE_ERROR
            ]);
            return new Result(
                ['token' => $token, 'user' => $user, 'mapper' => $mapper],
                $this->userOptions->getMessagesOptions()
                    ->getMessage(MessagesOptions::RESET_TOKEN_SAVE_ERROR)
            );
        } catch (\Exception $e) {
            $errorData = ['user' => $user, 'mapper' => $mapper];
            if ($token) {
                $errorData['token'] = $token;
            }
            $this->dispatchEvent(TokenEvent::EVENT_TOKEN_RESET_TOKEN_SAVE_ERROR, $errorData + ['error' => $e]);
            return new Result(['token' => $token, 'user' => $user, 'mapper' => $mapper], $e);
        }
    }
}
