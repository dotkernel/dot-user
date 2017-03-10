<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/13/2017
 * Time: 10:22 PM
 */

declare(strict_types = 1);

namespace Dot\User\Entity;

/**
 * Class AbstractSelectorTokenEntity
 * @package Dot\User\Entity
 */
abstract class AbstractSelectorTokenEntity extends AbstractTokenEntity
{
    /** @var  string */
    protected $selector;

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * @param string $selector
     */
    public function setSelector($selector)
    {
        $this->selector = $selector;
    }
}
