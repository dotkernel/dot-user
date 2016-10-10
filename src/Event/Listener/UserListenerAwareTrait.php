<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/10/2016
 * Time: 4:44 AM
 */

namespace Dot\User\Event\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class UserListenerAwareTrait
 * @package Dot\User\Event\Listener
 */
trait UserListenerAwareTrait
{
    use EventManagerAwareTrait;

    /** @var AbstractListenerAggregate[] */
    protected $listeners = [];

    /**
     * @param AbstractListenerAggregate $listener
     * @param int $priority
     * @return $this
     */
    public function attachUserListener(AbstractListenerAggregate $listener, $priority = 1)
    {
        $listener->attach($this->getEventManager(), $priority);
        $this->listeners[] = $listener;

        return $this;
    }

    /**
     * @param AbstractListenerAggregate $listener
     * @return $this
     */
    public function detachUserListener(AbstractListenerAggregate $listener)
    {
        $listener->detach($this->getEventManager());

        $idx = 0;
        foreach ($this->listeners as $l) {
            if ($l === $listener) {
                break;
            }

            $idx++;
        }

        unset($this->listeners[$idx]);
        return $this;
    }

    /**
     * @return $this
     */
    public function clearUserListeners()
    {
        foreach ($this->listeners as $listener) {
            $listener->detach($this->getEventManager());
        }

        $this->listeners = [];
        return $this;
    }
}