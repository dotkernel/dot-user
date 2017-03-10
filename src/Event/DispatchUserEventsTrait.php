<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 4:13 AM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Dot\User\Result\Result;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class DispatchUserEventsTrait
 * @package Dot\User\Event
 */
trait DispatchUserEventsTrait
{
    use EventManagerAwareTrait;

    /**
     * @param string $name
     * @param array $data
     * @param null $target
     * @return \Zend\EventManager\ResponseCollection
     */
    public function dispatchEvent(string $name, array $data = [], $target = null)
    {
        if ($target === null) {
            $target = $this;
        }

        $event = new UserEvent($name, $target, $data);
        return $this->getEventManager()->triggerEventUntil(function ($r) {
            return ($r instanceof Result);
        }, $event);
    }
}
