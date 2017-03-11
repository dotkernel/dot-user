<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\ListenerAggregateInterface;

/**
 * Interface TokenEventListenerInterface
 * @package Dot\User\Event
 */
interface TokenEventListenerInterface extends ListenerAggregateInterface
{
    public function onBeforeSaveConfirmToken(TokenEvent $e);

    public function onAfterSaveConfirmToken(TokenEvent $e);

    public function onConfirmTokenSaveError(TokenEvent $e);

    public function onBeforeSaveRememberToken(TokenEvent $e);

    public function onAfterSaveRememberToken(TokenEvent $e);

    public function onRememberTokenSaveError(TokenEvent $e);

    public function onBeforeValidateRememberToken(TokenEvent $e);

    public function onAfterValidateRememberToken(TokenEvent $e);

    public function onRememberTokenValidationError(TokenEvent $e);

    public function onBeforeSaveResetToken(TokenEvent $e);

    public function onAfterSaveResetToken(TokenEvent $e);

    public function onResetTokenSaveError(TokenEvent $e);
}
