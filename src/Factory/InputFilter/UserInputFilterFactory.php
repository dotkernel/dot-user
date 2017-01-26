<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 11/24/2016
 * Time: 8:22 PM
 */

namespace Dot\User\Factory\InputFilter;

use Dot\User\Form\InputFilter\UserInputFilter;
use Dot\User\Options\UserOptions;
use Interop\Container\ContainerInterface;
use Dot\Validator\Ems\NoRecordExists;

/**
 * Class UserInputFilterFactory
 * @package Dot\User\Factory\InputFilter
 */
class UserInputFilterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $service = $container->get('UserService');
        $inputFilter = new UserInputFilter(
            $container->get(UserOptions::class),
            new NoRecordExists([
                'service' => $service,
                'key' => 'email'
            ]),
            new NoRecordExists([
                'service' => $service,
                'key' => 'username'
            ])
        );
        $inputFilter->init();
        return $inputFilter;
    }
}
