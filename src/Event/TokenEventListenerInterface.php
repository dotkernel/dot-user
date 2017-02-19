<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 2:36 AM
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
