<?php
/**
 * @copyright: DotKernel
 * @library: dot-user
 * @author: n3vrax
 * Date: 2/21/2017
 * Time: 8:42 PM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Psr\Http\Message\ResponseInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\ResponseCollection;

/**
 * Trait DispatchControllerEventsTrait
 * @package Dot\User\Event
 */
trait DispatchControllerEventsTrait
{
    use EventManagerAwareTrait;

    /**
     * @param string $name
     * @param array $data
     * @param null $target
     * @return ControllerEvent|ResponseCollection
     */
    public function dispatchEvent(string $name, array $data = [], $target = null)
    {
        if ($target === null) {
            $target = $this;
        }

        $event = new ControllerEvent($name, $target, $data);
        $result = $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof ResponseInterface);
        }, $event);

        if ($result->stopped()) {
            return $result->last();
        }

        return $event;
    }
}
