<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/10/2016
 * Time: 4:42 AM
 */

namespace Dot\User\Event\Listener;

use Zend\EventManager\AbstractListenerAggregate;

/**
 * Interface UserListenerAwareInterface
 * @package Dot\User\Event\Listener
 */
interface UserListenerAwareInterface
{
    /**
     * @param AbstractListenerAggregate $listener
     * @param int $priority
     * @return mixed
     */
    public function attachUserListener(AbstractListenerAggregate $listener, $priority = 1);

    /**
     * @param AbstractListenerAggregate $listener
     * @return mixed
     */
    public function detachUserListener(AbstractListenerAggregate $listener);

    /**
     * @return mixed
     */
    public function clearUserListeners();
}