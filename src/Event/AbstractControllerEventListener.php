<?php
/**
 * @copyright: DotKernel
 * @library: dot-user
 * @author: n3vrax
 * Date: 2/21/2017
 * Time: 9:08 PM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\AbstractListenerAggregate;

/**
 * Class AbstractControllerEventListener
 * @package Dot\User\Event
 */
abstract class AbstractControllerEventListener extends AbstractListenerAggregate implements
    ControllerEventListenerInterface
{
    use ControllerEventListenerTrait;
}
