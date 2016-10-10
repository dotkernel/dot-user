<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace Dot\User\Mapper;

use Dot\Mapper\AbstractDbMapper;
use Dot\User\Entity\UserEntityInterface;
use Dot\User\Exception\InvalidArgumentException;
use Dot\User\Options\DbOptions;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;

/**
 * Class UserDbMapper
 * @package Dot\User\Mapper
 */
class UserDbMapper extends AbstractDbMapper implements UserMapperInterface
{
    protected $idColumn = 'id';

    /** @var  DbOptions */
    protected $dbOptions;

    /**
     * UserDbMapper constructor.
     * @param array|string|\Zend\Db\Sql\TableIdentifier $table
     * @param DbOptions $dbOptions
     * @param AdapterInterface $adapter
     * @param null $features
     * @param null $resultSetPrototype
     * @param null $sql
     */
    public function __construct(
        $table,
        DbOptions $dbOptions,
        AdapterInterface $adapter,
        $features = null,
        $resultSetPrototype = null,
        $sql = null)
    {
        parent::__construct($table, $adapter, $features, $resultSetPrototype, $sql);
        $this->dbOptions = $dbOptions;
    }

    /**
     * @param $id
     * @return UserEntityInterface|mixed|null
     */
    public function findUser($id)
    {
        return $this->findUserBy($this->idColumn, $id);
    }

    /**
     * @param $field
     * @param $value
     * @return UserEntityInterface|null
     */
    public function findUserBy($field, $value)
    {
        $result = $this->select([$field => $value]);
        return $result->current();
    }

    /**
     * @param array $filters
     * @return \Zend\Db\ResultSet\ResultSet
     */
    public function findAllUsers(array $filters = [])
    {
        return $this->select();
    }

    /**
     * @param $data
     * @return int
     */
    public function createUser($data)
    {
        if($data instanceof UserEntityInterface) {
            $data = $this->entityToArray($data);
        }

        return $this->insert($data);
    }

    public function updateUser($data)
    {
        if($data instanceof UserEntityInterface) {
            $data = $this->entityToArray($data);
        }

        if(isset($data[$this->idColumn])) {
            $id = $data[$this->idColumn];
            unset($data[$this->idColumn]);

            return $this->update($data, [$this->idColumn => $id]);
        }
        else {
            throw new InvalidArgumentException('Cannot update user - missing id field');
        }
    }

    /**
     * @param $id
     * @return int
     */
    public function removeUser($id)
    {
        return $this->delete([$this->idColumn => $id]);
    }

    /**
     * @param $data
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function saveResetToken($data)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserResetTokenTable());
        $insert = $sql->insert();
        $insert->columns(array_keys($data))->values($data);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    /**
     * @param $userId
     * @param $token
     * @return mixed
     */
    public function findResetToken($userId, $token)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserResetTokenTable());
        $select = $sql->select()->where(['userId' => $userId, 'token' => $token]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }

    /**
     * @param $data
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function saveConfirmToken($data)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
        $insert = $sql->insert();
        $insert->columns(array_keys($data))->values($data);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    /**
     * @param $userId
     * @param $token
     * @return mixed
     */
    public function findConfirmToken($userId, $token)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
        $select = $sql->select()->where(['userId' => $userId, 'token' => $token]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }

    /**
     * @param $userId
     * @param $token
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function removeConfirmToken($userId, $token)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
        $delete = $sql->delete()->where(['userId' => $userId, 'token' => $token]);

        $stmt = $sql->prepareStatementForSqlObject($delete);
        return $stmt->execute();
    }

    /**
     * @param $data
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function saveRememberToken($data)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserRememberTokenTable());
        $insert = $sql->insert();
        $insert->columns(array_keys($data))->values($data);

        $stmt = $sql->prepareStatementForSqlObject($insert);
        return $stmt->execute();
    }

    /**
     * @param $selector
     * @return mixed
     */
    public function findRememberToken($selector)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserRememberTokenTable());
        $select = $sql->select()->where(['selector' => $selector]);

        $stmt = $sql->prepareStatementForSqlObject($select);
        return $stmt->execute()->current();
    }

    /**
     * @param $userId
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function removeRememberToken($userId)
    {
        $sql = new Sql($this->getAdapter(), $this->dbOptions->getUserRememberTokenTable());
        $delete = $sql->delete()->where(['userId' => $userId]);

        $stmt = $sql->prepareStatementForSqlObject($delete);
        return $stmt->execute();
    }
}