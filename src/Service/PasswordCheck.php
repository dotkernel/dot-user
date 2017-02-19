<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/15/2017
 * Time: 2:26 PM
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
