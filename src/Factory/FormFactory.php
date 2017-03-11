<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
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
