<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Result;

/**
 * Class Result
 * @package Dot\User\Result
 */
class Result implements ResultInterface
{
    /** @var  mixed */
    protected $error;

    /** @var array */
    protected $params = [];

    /**
     * Result constructor.
     * @param array $params
     * @param null $error
     */
    public function __construct(array $params = [], $error = null)
    {
        $this->params = $params;
        $this->error = $error;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getParam($name)
    {
        return $this->params[$name] ?? null;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return is_null($this->error);
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return isset($this->error);
    }

    /**
     * @return bool
     */
    public function hasException(): bool
    {
        return $this->error instanceof \Exception;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }
}
