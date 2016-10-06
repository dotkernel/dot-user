<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/24/2016
 * Time: 7:47 PM
 */

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Dot\User\Service\PasswordDefault;
use Interop\Container\ContainerInterface;

/**
 * Class PasswordDefaultFactory
 * @package Dot\User\Factory
 */
class PasswordDefaultFactory
{
    /**
     * @param ContainerInterface $container
     * @return PasswordDefault
     */
    public function __invoke(ContainerInterface $container)
    {
        $options = $container->get(UserOptions::class);
        return new PasswordDefault($options);
    }
}