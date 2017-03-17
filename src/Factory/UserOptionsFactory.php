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

/**
 * Class UserOptionsFactory
 * @package Dot\User\Factory
 */
class UserOptionsFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return UserOptions
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        return new $requestedName($container->get('config')['dot_user']);
    }
}
