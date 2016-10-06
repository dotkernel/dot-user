<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/29/2016
 * Time: 8:11 PM
 */

namespace Dot\User;


trait FlashMessagesTrait
{
    public function addError($error, FlashMessengerPlugin $messenger)
    {
        $error = (array) $error;
        foreach ($error as $e) {
            $messenger->addError($e);
        }
    }

    public function addInfo($message, FlashMessengerPlugin $messenger)
    {
        $message = (array) $message;
        foreach ($message as $m) {
            $messenger->addInfo($m);
        }
    }

    public function addWarning($message, FlashMessengerPlugin $messenger)
    {
        $message = (array) $message;
        foreach ($message as $m) {
            $messenger->addWarning($m);
        }
    }

    public function addSuccess($message, FlashMessengerPlugin $messenger)
    {
        $message = (array) $message;
        foreach ($message as $m) {
            $messenger->addSuccess($m);
        }
    }
}