<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/14/2017
 * Time: 12:30 AM
 */

declare(strict_types = 1);

namespace Dot\User\Result;

/**
 * Interface ResultInterface
 * @package Dot\User\Result
 */
interface ResultInterface
{
    /**
     * @param $name
     * @param $value
     */
    public function setParam($name, $value);

    /**
     * @param $name
     * @return mixed
     */
    public function getParam($name);

    /**
     * @param array $params
     */
    public function setParams(array $params);

    /**
     * @return bool
     */
    public function isValid(): bool;

    /**
     * @return bool
     */
    public function hasError(): bool;

    /**
     * @return bool
     */
    public function hasException(): bool;

    /**
     * @return mixed
     */
    public function getError();

    /**
     * @param $error
     */
    public function setError($error);
}
