<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 7/21/2016
 * Time: 7:56 PM
 */

namespace Dot\User\Factory\Form;

use Dot\User\Form\UserFormManager;
use Interop\Container\ContainerInterface;

/**
 * Class FormManagerFactory
 * @package Dot\User\Factory\Form
 */
class UserFormManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @return UserFormManager
     */
    public function __invoke(ContainerInterface $container)
    {
        return new UserFormManager($container, $container->get('config')['dot_user']['form_manager']);
    }
}