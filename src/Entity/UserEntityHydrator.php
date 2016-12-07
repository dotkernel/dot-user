<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 7:56 PM
 */

namespace Dot\User\Entity;

use Zend\Hydrator\ClassMethods;
use Zend\Hydrator\Filter\FilterComposite;
use Zend\Hydrator\Filter\MethodMatchFilter;

/**
 * Class UserEntityHydrator
 * @package Dot\User\Entity
 */
class UserEntityHydrator extends ClassMethods
{
    /**
     * UserEntityHydrator constructor.
     */
    public function __construct()
    {
        parent::__construct(false);
        $this->addFilter('name', new MethodMatchFilter('getName'), FilterComposite::CONDITION_AND);
        $this->addFilter('roles', new MethodMatchFilter('getRoles'), FilterComposite::CONDITION_AND);
    }
}