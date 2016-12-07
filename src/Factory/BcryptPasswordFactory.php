<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 11/23/2016
 * Time: 7:41 PM
 */

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Zend\Crypt\Password\Bcrypt;

/**
 * Class BcryptPasswordFactory
 * @package Dot\User\Factory
 */
class BcryptPasswordFactory
{
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