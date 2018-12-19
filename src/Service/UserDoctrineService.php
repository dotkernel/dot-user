<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Service;

use Doctrine\DBAL\LockMode;
use Dot\Doctrine\Mapper\EntityManagerAwareInterface;
use Dot\Doctrine\Mapper\EntityManagerAwareTrait;
use Dot\Mapper\Mapper\MapperManagerAwareInterface;
use Dot\Mapper\Mapper\MapperManagerAwareTrait;
use Dot\User\Entity\ResetTokenEntity;
use Dot\User\Entity\UserEntity;
use Dot\User\Entity\UserEntityRepository;
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
class UserDoctrineService extends UserService implements
    UserServiceInterface,
    UserEventListenerInterface,
    TokenEventListenerInterface,
    EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;
    /*
    use DispatchUserEventsTrait;

    use UserEventListenerTrait,
        TokenEventListenerTrait {
        UserEventListenerTrait::attach as userEventAttach;
        TokenEventListenerTrait::attach as tokenEventAttach;
        UserEventListenerTrait::detach as userEventDetach;
        TokenEventListenerTrait::detach as tokenEventDetach;
    }/**/

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

    public function find($id, array $options = []): ?UserEntity
    {
        $id = (int)$id;
        /** @var UserEntityRepository $repository */
        $repository = $this->entityManager->getRepository(UserEntity::class);
        return $repository->getUser($id);
    }

    public function findByEmail(string $email, array $options = []): ?UserEntity
    {
        return $this->entityManager->getRepository(UserEntity::class)->findBy(['email' => $email], null, 1);
    }
    /*
    public function delete(UserEntity $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }//*/
}
