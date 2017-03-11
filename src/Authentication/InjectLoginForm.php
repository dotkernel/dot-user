<?php
/**
 * @see https://github.com/dotkernel/dot-user/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-user/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\User\Authentication;

use Dot\Authentication\Web\Event\AbstractAuthenticationEventListener;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\User\Options\UserOptions;

/**
 * Class InjectLoginFormListener
 * @package Dot\User\Authentication
 */
class InjectLoginForm extends AbstractAuthenticationEventListener
{
    /** @var  FormsPlugin */
    protected $formsPlugin;

    /** @var  UserOptions */
    protected $userOptions;

    /**
     * InjectLoginFormListener constructor.
     * @param FormsPlugin $formsPlugin
     * @param UserOptions $userOptions
     */
    public function __construct(FormsPlugin $formsPlugin, UserOptions $userOptions)
    {
        $this->userOptions = $userOptions;
        $this->formsPlugin = $formsPlugin;
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onBeforeAuthentication(AuthenticationEvent $e)
    {
        $formsPlugin = $this->formsPlugin;
        $form = $formsPlugin('Login');

        $e->setParam('form', $form);
    }

    /**
     * @param AuthenticationEvent $e
     */
    public function onAuthenticationBeforeRender(AuthenticationEvent $e)
    {
        $formsPlugin = $this->formsPlugin;
        $form = $formsPlugin('Login');

        $e->setParam('form', $form);
        // overwrite the login template with the one configured for this module
        $e->setParam('template', $this->userOptions->getTemplateOptions()->getLoginTemplate());
        $e->setParam('passwordRecoveryEnabled', $this->userOptions->getPasswordRecoveryOptions()->isEnableRecovery());
    }
}
