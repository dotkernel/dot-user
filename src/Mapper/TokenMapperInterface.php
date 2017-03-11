<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Mapper;

use Dot\Mapper\Mapper\MapperInterface;
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
