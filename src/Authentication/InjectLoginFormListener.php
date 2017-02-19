<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 7:13 PM
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
class InjectLoginFormListener extends AbstractAuthenticationEventListener
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
    public function onAuthenticate(AuthenticationEvent $e)
    {
        $formsPlugin = $this->formsPlugin;
        $form = $formsPlugin('Login');

        $e->setParam('form', $form);
        $e->setParam('passwordRecoveryEnabled', $this->userOptions->getPasswordRecoveryOptions()->isEnableRecovery());
        $e->setParam('showLabels', $this->userOptions->isShowFormLabels());
        $e->setParam('layoutTemplate', $this->userOptions->getTemplateOptions()->getLoginTemplateLayout());
    }
}
