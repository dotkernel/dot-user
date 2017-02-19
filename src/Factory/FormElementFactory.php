<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/17/2017
 * Time: 9:22 PM
 */

declare(strict_types = 1);

namespace Dot\User\Factory;

use Dot\User\Options\UserOptions;
use Dot\User\Options\UserOptionsAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\Form\Element;

/**
 * Class FormFactory
 * @package Dot\User\Factory
 */
class FormElementFactory
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array $options
     * @return Element
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = [])
    {
        /** @var Element $formElement */
        $formElement = new $requestedName($options);
        if ($formElement instanceof UserOptionsAwareInterface) {
            $formElement->setUserOptions($container->get(UserOptions::class));
        }

        return $formElement;
    }
}
