<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Dot\Event\Event;
use Psr\Http\Message\ResponseInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\ResponseCollection;

/**
 * Trait DispatchControllerEventsTrait
 * @package Dot\User\Event
 */
trait DispatchUserControllerEventsTrait
{
    use EventManagerAwareTrait;

    public function dispatchEvent(string $name, array $data = [], $target = null):Event|ResponseCollection
    {
        if ($target === null) {
            $target = $this;
        }

        $event = new UserControllerEvent($name, $target, $data);
        $result = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        if ($result->stopped()) {
            return $result->last();
        }

        return $event;
    }
}
