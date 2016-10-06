<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/6/2016
 * Time: 11:03 PM
 */

namespace Dot\User\Result;

/**
 * Interface ResultInterface
 * @package Dot\User\Result
 */
interface ResultInterface
{
    /**
     * Get error message when error occurs
     * @return string[]
     */
    public function getMessages();

    /**
     * Tells if the MailService that produced this result was properly sent
     * @return bool
     */
    public function isValid();

    /**
     * Tells if Result has an Exception
     * @return bool
     */
    public function hasException();

    /**
     * @return \Exception
     */
    public function getException();
}