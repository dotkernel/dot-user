<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 2:51 AM
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Zend\EventManager\AbstractListenerAggregate;

/**
 * Class AbstractTokenEventListener
 * @package Dot\User\Event
 */
abstract class AbstractTokenEventListener extends AbstractListenerAggregate implements TokenEventListenerInterface
{
    use TokenEventListenerTrait;
}
