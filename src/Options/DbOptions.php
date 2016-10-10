<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/6/2016
 * Time: 7:39 PM
 */

namespace Dot\User\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class DbOptions
 * @package Dot\User\Options
 */
class DbOptions extends AbstractOptions
{
    /** @var  string */
    protected $dbAdapter;

    /** @var  string */
    protected $userTable = 'user';

    /** @var  string */
    protected $userResetTokenTable = 'user_reset_token';

    /** @var  string */
    protected $userConfirmTokenTable = 'user_confirm_token';

    /** @var string */
    protected $userRememberTokenTable = 'user_remember_token';

    /**
     * @return string
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }

    /**
     * @param string $dbAdapter
     * @return DbOptions
     */
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserTable()
    {
        return $this->userTable;
    }

    /**
     * @param string $userTable
     * @return DbOptions
     */
    public function setUserTable($userTable)
    {
        $this->userTable = $userTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserResetTokenTable()
    {
        return $this->userResetTokenTable;
    }

    /**
     * @param string $userResetTokenTable
     * @return DbOptions
     */
    public function setUserResetTokenTable($userResetTokenTable)
    {
        $this->userResetTokenTable = $userResetTokenTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserConfirmTokenTable()
    {
        return $this->userConfirmTokenTable;
    }

    /**
     * @param string $userConfirmTokenTable
     * @return DbOptions
     */
    public function setUserConfirmTokenTable($userConfirmTokenTable)
    {
        $this->userConfirmTokenTable = $userConfirmTokenTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserRememberTokenTable()
    {
        return $this->userRememberTokenTable;
    }

    /**
     * @param string $userRememberTokenTable
     * @return DbOptions
     */
    public function setUserRememberTokenTable($userRememberTokenTable)
    {
        $this->userRememberTokenTable = $userRememberTokenTable;
        return $this;
    }


}