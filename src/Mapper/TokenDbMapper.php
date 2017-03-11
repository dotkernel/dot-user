<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Mapper;

use Dot\Mapper\Mapper\AbstractDbMapper;
use Dot\User\Entity\AbstractSelectorTokenEntity;

/**
 * Class TokenDbMapper
 * @package Dot\User\Mapper
 */
class TokenDbMapper extends AbstractDbMapper implements TokenMapperInterface
{
    /** @var string */
    protected $table = 'user_token';

    /**
     * @param string $selector
     * @param array $options
     * @return AbstractSelectorTokenEntity|null
     */
    public function getBySelector(string $selector, array $options = []): ?AbstractSelectorTokenEntity
    {
        $options['conditions'] = array_merge(['selector' => $selector], $options['conditions'] ?? []);
        $finder = (string)($options['finder'] ?? 'all');
        $result = $this->find($finder, $options);
        if (!empty($result) && isset($result[0])) {
            return $result[0];
        }

        return null;
    }
}
