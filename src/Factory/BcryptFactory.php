<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/15/2017
 * Time: 2:24 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Crypt\Password\Bcrypt;

/**
 * Class BcryptFactory
 * @package Dot\User\Factory
 */
class BcryptFactory
{
    /**
     * @param ContainerInterface $container
     * @return Bcrypt
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var UserOptions $userOptions */
        $userOptions = $container->get(UserOptions::class);

        $cost = $userOptions->getPasswordCost();
        $bcrypt = new Bcrypt();
        $bcrypt->setCost($cost);

        return $bcrypt;
    }
}
