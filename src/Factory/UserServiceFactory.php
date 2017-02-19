<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/16/2017
 * Time: 10:43 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Dot\User\Service\TokenServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\Crypt\Password\PasswordInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class UserServiceFactory
 * @package Dot\User\Factory
 */
class UserServiceFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $options = [];
        $options['user_options'] = $container->get(UserOptions::class);
        $options['token_service'] = $container->get(TokenServiceInterface::class);
        $options['password_service'] = $container->get(PasswordInterface::class);
        $options['event_manager'] = $container->get(EventManagerInterface::class);

        return new $requestedName($options);
    }
}
