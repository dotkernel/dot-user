<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Psr\Container\ContainerInterface;
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
