<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Event;

use Laminas\EventManager\AbstractListenerAggregate;

/**
 * Class AbstractTokenEventListener
 * @package Dot\User\Event
 */
abstract class AbstractTokenEventListener extends AbstractListenerAggregate implements TokenEventListenerInterface
{
    use TokenEventListenerTrait;
}
