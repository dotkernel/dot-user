<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/15/2017
 * Time: 2:49 PM
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
