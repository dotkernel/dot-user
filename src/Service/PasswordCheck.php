<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Service;

use Zend\Crypt\Password\PasswordInterface;

/**
 * Class PasswordCheck
 * @package Dot\User\Service
 */
class PasswordCheck
{
    /** @var  PasswordInterface */
    protected $passwordService;

    public function __construct(PasswordInterface $passwordService)
    {
        $this->passwordService = $passwordService;
    }

    /**
     * @param string $hash
     * @param string $password
     * @return bool
     */
    public function __invoke(string $hash, string $password): bool
    {
        return $this->passwordService->verify($password, $hash);
    }
}
