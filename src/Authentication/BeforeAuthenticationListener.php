<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vrax
 * Date: 2/18/2017
 * Time: 7:10 PM
 */

declare(strict_types = 1);

namespace Dot\User\Authentication;

use Dot\Authentication\Web\Event\AbstractAuthenticationEventListener;
use Dot\Authentication\Web\Event\AuthenticationEvent;
use Dot\Controller\Plugin\Forms\FormsPlugin;
use Dot\User\Form\LoginForm;

/**
 * Class BeforeAuthenticationListener
 * @package Dot\User\Authentication
 */
class BeforeAuthenticationListener extends AbstractAuthenticationEventListener
{
    /** @var  FormsPlugin */
    protected $formsPlugin;

    /**
     * BeforeAuthenticationListener constructor.
     * @param FormsPlugin $formsPlugin
     */
    public function __construct(FormsPlugin $formsPlugin)
    {
        $this->formsPlugin = $formsPlugin;
    }

    /**
     * Use the previously injected form to validate login data
     * @param AuthenticationEvent $e
     */
    public function onAuthenticate(AuthenticationEvent $e)
    {
        $request = $e->getRequest();
        $form = $e->getParam('form', null);

        if ($request->getMethod() === 'POST') {
            $data = $request->getParsedBody();

            if ($form instanceof LoginForm) {
                $form->setData($data);
                if (!$form->isValid()) {
                    $messages = $this->formsPlugin->getMessages($form);
                    $e->setError($messages);
                    $this->formsPlugin->saveState($form);
                    return;
                }

                $data = array_merge($e->getParams(), $data, $form->getData());
                $e->setParams($data);
            }
        }
    }
}
