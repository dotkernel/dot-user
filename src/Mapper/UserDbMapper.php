<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 7:55 PM
 */

namespace Dot\User\Mapper;

use Dot\Ems\Mapper\RelationalDbMapper;
use Dot\User\Options\DbOptions;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Hydrator\HydratorInterface;

/**
 * Class UserDbMapper
 * @package Dot\User\Mapper
 */
class UserDbMapper extends RelationalDbMapper implements UserMapperInterface
{
    /** @var  DbOptions */
    protected $dbOptions;

    /**
     * UserDbMapper constructor.
     * @param $table
     * @param Adapter $adapter
     * @param DbOptions $dbOptions
     * @param null|HydratorInterface $prototype
     * @param HydratorInterface|null $hydrator
     * @param null $features
     */
    public function __construct($table, Adapter $adapter, DbOptions $dbOptions, $prototype,
        HydratorInterface $hydrator = null, $features = null)
    {
        parent::__construct($table, $adapter, $prototype, $hydrator, $features);
        $this->dbOptions = $dbOptions;
    }


    /**
     * @param $data
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public function saveResetToken($data)
    {
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserResetTokenTable());
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
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserResetTokenTable());
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
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
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
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
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
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserConfirmTokenTable());
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
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserRememberTokenTable());
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
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserRememberTokenTable());
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
        $sql = new Sql($this->getTableGateway()->getAdapter(), $this->dbOptions->getUserRememberTokenTable());
        $delete = $sql->delete()->where(['userId' => $userId]);

        $stmt = $sql->prepareStatementForSqlObject($delete);
        return $stmt->execute();
    }
}