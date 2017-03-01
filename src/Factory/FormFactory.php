<?php
/**
 * @copyright: DotKernel
 * @library: dot-user
 * @author: n3vrax
 * Date: 3/1/2017
 * Time: 8:43 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Interop\Container\ContainerInterface;

/**
 * Class UserOptionsAwareDelegator
 * @package Dot\User\Factory
 */
class FormFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @return mixed
     */
    public function __invoke(ContainerInterface $container, $requestedName)
    {
        $form = new $requestedName();
        if ($form instanceof UserOptionsAwareInterface) {
            $form->setUserOptions($container->get(UserOptions::class));
        }

        return $form;
    }
}
