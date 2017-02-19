<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 9:35 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Authentication\BeforeLogoutListener;
use Dot\User\Service\TokenServiceInterface;
use Interop\Container\ContainerInterface;

/**
 * Class BeforeLogoutListenerFactory
 * @package Dot\User\Factory
 */
class BeforeLogoutListenerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $tokenService = $container->get(TokenServiceInterface::class);
        return new BeforeLogoutListener($tokenService);
    }
}
