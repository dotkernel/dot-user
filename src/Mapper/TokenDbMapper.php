<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/13/2017
 * Time: 10:42 PM
 */

declare(strict_types = 1);

namespace Dot\User\Mapper;

use Dot\Ems\Mapper\AbstractDbMapper;
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
