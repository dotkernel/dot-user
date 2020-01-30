<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Dot\User\Result\Result;
use Laminas\EventManager\EventManagerAwareTrait;

/**
 * Class DispatchTokenEventsTrait
 * @package Dot\User\Event
 */
trait DispatchTokenEventsTrait
{
    use EventManagerAwareTrait;

    /**
     * @param string $name
     * @param array $data
     * @param null $target
     * @return \Laminas\EventManager\ResponseCollection
     */
    public function dispatchEvent(string $name, array $data = [], $target = null)
    {
        if ($target === null) {
            $target = $this;
        }

        $event = new TokenEvent($name, $target, $data);
        return $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof Result);
        }, $event);
    }
}
