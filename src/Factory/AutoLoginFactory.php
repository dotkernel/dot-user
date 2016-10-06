<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 7/14/2016
 * Time: 12:03 AM
 */

namespace Dot\User\Factory;

use Dot\Authentication\AuthenticationInterface;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\User\Middleware\AutoLogin;
use Dot\User\Options\UserOptions;
use Dot\User\Service\UserServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;

/**
 * Class AutoLoginFactory
 * @package Dot\User\Factory
 */
class AutoLoginFactory
{
    /**
     * @param ContainerInterface $container
     * @return AutoLogin
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AutoLogin(
            $container->get(AuthenticationInterface::class),
            $container->get(UserServiceInterface::class),
            $container->get(UrlHelper::class),
            $container->get(FlashMessengerInterface::class),
            $container->get(UserOptions::class)
        );
    }
}