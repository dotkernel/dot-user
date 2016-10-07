<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/6/2016
 * Time: 11:05 PM
 */

namespace Dot\User\Result;

/**
 * Class AbstractResult
 * @package Dot\User\Result
 */
abstract class AbstractResult implements ResultInterface
{
    const DEFAULT_MESSAGE = 'Success!';

    /** @var bool  */
    protected $valid = true;

    /** @var  string[] */
    protected $messages = [];

    /** @var  \Exception */
    protected $exception;

    /**
     * AbstractResult constructor.
     * @param bool $valid
     * @param string|array $messages
     * @param \Exception|null $exception
     */
    public function __construct($valid = true, $messages = self::DEFAULT_MESSAGE, \Exception $exception = null)
    {
        $this->valid = $valid;
        $this->messages = (array) $messages;
        $this->exception = $exception;
    }

    /**
     * @return array|\string[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function hasException()
    {
        return ($this->exception instanceof \Exception);
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }


}