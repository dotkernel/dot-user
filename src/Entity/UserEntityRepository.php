<?php
/**
 * see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

namespace Dot\User\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Dot\Authentication\Identity\IdentityInterface as AuthenticationIdentity;
use Dot\Authorization\Identity\IdentityInterface as AuthorizationIdentity;
use Doctrine\ORM\Mapping as ORM;
use Dot\User\Entity\UserEntity as User;
use Dot\User\Entity\RoleEntity as Role;
// use Dot\Mapper\Entity\Entity;

class UserEntityRepository extends EntityRepository
{
    /**
     * @param User $user
     */
    public function saveUser(User $user)
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $id
     * @return User
     * @throws EntityNotFoundException
     */
    public function getUser(int $id): User
    {
        /** @var User $user */
        $user = $this->getEntityManager()->find(User::class, $id);
        if (!$user) {
            throw new EntityNotFoundException('Could not find user entity with ID: ' . $id);
        }

        return $user;
    }

    /**
     * @param string $username
     * @return User
     * @throws EntityNotFoundException
     */
    public function getUserByUsername(string $username): User
    {
        $user = $this->findUserByUsername($username);
        if (!$user) {
            throw new EntityNotFoundException('Could not find user entity with username: ' . $username);
        }

        return $user;
    }

    /**
     * @param string $email
     * @return null|User
     */
    public function findUserByEmail(string $email): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['email' => $email]);
        return $user;
    }

    /**
     * @param string $username
     * @return null|User
     */
    public function findUserByUsername(string $username): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['username' => $username]);
        return $user;
    }

    /**
     * @param EmailToken $emailToken
     * @return null|User
     */
    public function findUserByEmailToken(EmailToken $emailToken): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['emailToken' => $emailToken->__toString()]);
        return $user;
    }

    /**
     * @param string $identifier
     * @return null|User
     */
    public function findUserBySocialIdentifier(string $identifier): ?User
    {
        /** @var User|null $user */
        $user = $this->findOneBy(['socialIdentifier' => $identifier]);
        return $user;
    }

    /**
     * This is not the best place for role fetching,
     * should have it's own repository(will be refactored on next iteration)
     * @param string $name
     * @return Role
     * @throws EntityNotFoundException
     */
    public function getRoleByName(string $name): Role
    {
        $role = $this->getEntityManager()->getRepository(Role::class)->findOneBy(['name' => $name]);
        if (!$role instanceof Role) {
            throw new EntityNotFoundException('User role with name ' . $name . ' could not be found');
        }

        return $role;
    }

}