<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Service\PasswordCheck;
use Interop\Container\ContainerInterface;
use Zend\Crypt\Password\PasswordInterface;

/**
 * Class PasswordCheckFactory
 * @package Dot\User\Factory
 */
class PasswordCheckFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new PasswordCheck($container->get(PasswordInterface::class));
    }
}
