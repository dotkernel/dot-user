<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/14/2017
 * Time: 2:06 AM
 */

declare(strict_types = 1);

namespace Dot\User\Mapper;

use Dot\Ems\Mapper\MapperInterface;
use Dot\User\Entity\AbstractSelectorTokenEntity;

/**
 * Interface TokenMapperInterface
 * @package Dot\User\Mapper
 */
interface TokenMapperInterface extends MapperInterface
{
    /**
     * @param string $selector
     * @param array $options
     * @return AbstractSelectorTokenEntity|null
     */
    public function getBySelector(string $selector, array $options = []): ?AbstractSelectorTokenEntity;
}
