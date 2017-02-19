<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 2:34 AM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\AbstractListenerAggregate;

/**
 * Class AbstractUserEventListener
 * @package Dot\User\Event
 */
abstract class AbstractUserEventListener extends AbstractListenerAggregate implements UserEventListenerInterface
{
    use UserEventListenerTrait;
}
