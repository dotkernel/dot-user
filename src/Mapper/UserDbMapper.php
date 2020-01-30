<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Mapper;

use Dot\Mapper\Event\MapperEvent;
use Dot\Mapper\Mapper\AbstractDbMapper;
use Dot\Mapper\Mapper\MapperInterface;
use Dot\Mapper\Mapper\MapperManager;
use Dot\User\Entity\RoleEntity;
use Dot\User\Entity\UserEntity;
use Dot\User\Exception\RuntimeException;
use Dot\User\Options\UserOptions;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;

/**
 * Class UserDbMapper
 * @package Dot\User\Mapper
 */
class UserDbMapper extends AbstractDbMapper implements UserMapperInterface
{
    /** @var string */
    protected $table = 'user';

    /** @var string */
    protected $rolesIntersectionTable = 'user_roles';

    /** @var  UserOptions */
    protected $userOptions;

    /**
     * UserDbMapper constructor.
     * @param MapperManager $mapperManager
     * @param array $options
     */
    public function __construct(MapperManager $mapperManager, array $options = [])
    {
        if (isset($options['user_options']) && $options['user_options'] instanceof UserOptions) {
            $this->userOptions = $options['user_options'];
        }

        parent::__construct($mapperManager, $options);
    }

    /**
     * @param MapperEvent $e
     */
    public function onAfterLoad(MapperEvent $e)
    {
        /** @var UserEntity $entity */
        $user = $e->getParam('entity');

        //maybe we can skip roles queries, if entity already has roles set
        if (empty($user->getRoles())) {
            $select = $this->getSlaveSql()->select()->from($this->rolesIntersectionTable)
                ->where(['userId' => $user->getId()]);

            $stmt = $this->getSlaveSql()->prepareStatementForSqlObject($select);
            $result = $stmt->execute();
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet(ResultSet::TYPE_ARRAY);
                $resultSet->initialize($result);

                $roleIds = [];
                foreach ($resultSet as $row) {
                    $roleIds[] = $row['roleId'];
                }

                if (!empty($roleIds)) {
                    /** @var MapperInterface $rolesMapper */
                    $rolesMapper = $this->mapperManager->get($this->userOptions->getRoleEntity());
                    $roles = $rolesMapper->find('all', [
                        'conditions' => ['id' => $roleIds]
                    ]);

                    $user->setRoles($roles);
                } else {
                    $user->setRoles([]);
                }
            } else {
                $user->setRoles([]);
            }
        }
    }

    /**
     * @param MapperEvent $e
     */
    public function onAfterSave(MapperEvent $e)
    {
        // by default, all this code happens in a transaction
        /** @var UserEntity $entity */
        $user = $e->getParam('entity');
        if (empty($user->getRoles())) {
            // set the user with the default role as set in config
            $defaultRoles = $this->userOptions->getDefaultRoles();
            if (!empty($defaultRoles)) {
                /** @var MapperInterface $rolesMapper */
                $rolesMapper = $this->mapperManager->get($this->userOptions->getRoleEntity());

                $roles = $rolesMapper->find('all', ['conditions' => ['name' => $defaultRoles]]);
                if (!empty($roles)) {
                    $user->setRoles($roles);
                }
            }
        }

        // save user roles too
        // 1. delete all from intersection table
        // 2. repopulate user to roles intersection table
        $delete = $this->getSql()->delete($this->rolesIntersectionTable)->where(['userId' => $user->getId()]);
        $this->getSql()->prepareStatementForSqlObject($delete)->execute();

        $roles = $user->getRoles();
        $stmt = $this->getAdapter()->createStatement(
            'INSERT INTO `' . $this->rolesIntersectionTable . '` VALUES (?, ?)'
        );

        /** @var RoleEntity $role */
        foreach ($roles as $role) {
            $result = $stmt->execute([$user->getId(), $role->getId()]);
            if ($result->getAffectedRows() < 1) {
                throw new RuntimeException('Failed to insert user to role association');
            }
        }
    }

    /**
     * @param string $email
     * @param array $options
     * @return UserEntity|null
     */
    public function getByEmail(string $email, array $options = []): ?UserEntity
    {
        $options['conditions'] = ['email' => $email];
        $finder = (string)($options['finder'] ?? 'all');
        $result = $this->find($finder, $options);
        if (!empty($result) && isset($result[0])) {
            return $result[0];
        }

        return null;
    }
}
