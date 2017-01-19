<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-user
 * @author: n3vrax
 * Date: 6/20/2016
 * Time: 7:54 PM
 */

namespace Dot\User;

use Dot\User\Controller\UserController;
use Dot\User\Factory\AuthenticationListenerFactory;
use Dot\User\Factory\AutoLoginFactory;
use Dot\User\Factory\BcryptPasswordFactory;
use Dot\User\Factory\BootstrapFactory;
use Dot\User\Factory\Fieldset\UserFieldsetFactory;
use Dot\User\Factory\Form\UserFormManagerFactory;
use Dot\User\Factory\InputFilter\UserInputFilterFactory;
use Dot\User\Factory\PasswordCheckFactory;
use Dot\User\Factory\UserControllerFactory;
use Dot\User\Factory\UserDbMapperFactory;
use Dot\User\Factory\UserOptionsFactory;
use Dot\User\Factory\UserServiceFactory;
use Dot\User\Form\Fieldset\UserFieldset;
use Dot\User\Form\InputFilter\UserInputFilter;
use Dot\User\Form\UserFormManager;
use Dot\User\Listener\AuthenticationListener;
use Dot\User\Mapper\UserDbMapper;
use Dot\User\Middleware\AutoLogin;
use Dot\User\Middleware\Bootstrap;
use Dot\User\Options\UserOptions;
use Dot\User\Service\PasswordCheck;
use Dot\User\Service\UserService;
use Zend\Crypt\Password\PasswordInterface;

/**
 * Class ConfigProvider
 * @package Dot\User
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke()
    {
        return [

            'dependencies' => $this->getDependencyConfig(),

            'middleware_pipeline' => [
                [
                    'middleware' => [
                        Bootstrap::class,
                        AutoLogin::class,
                    ],
                    'priority' => 9999,
                ]
            ],

            'dot_user' => [

                'user_listeners' => [],

                'db_options' => [],

                'register_options' => [],

                'login_options' => [],

                'password_recovery_options' => [],

                'confirm_account_options' => [],

                'messages_options' => [
                    'messages' => []
                ],

                'template_options' => [],

                'form_manager' => [],
            ],

            'dot_authentication' => [

                'web' => [
                    'login_route' => 'login',
                    'logout_route' => 'logout',

                    'login_template' => 'dot-user::login',

                    'after_logout_route' => 'login',
                    'after_login_route' => 'home',

                    'allow_redirect_param' => true,
                    'redirect_param_name' => 'redirect',
                ]

            ],

            'routes' => [
                'login_route' => [
                    'name' => 'login',
                    'path' => '/user/login',
                ],
                'logout_route' => [
                    'name' => 'logout',
                    'path' => '/user/logout',
                ],
                'user_route' => [
                    'name' => 'user',
                    'path' => '/user[/{action}]',
                    'middleware' => [
                        //we keep this as array so that other controllers can be inserted to the same path
                        UserController::class
                    ],
                ],
            ],

            'templates' => [
                'paths' => [
                    'dot-user' => [realpath(__DIR__ . '/../templates/dot-user')],
                ]
            ],
        ];
    }

    public function getDependencyConfig()
    {
        return [
            'factories' => [
                UserOptions::class => UserOptionsFactory::class,

                UserController::class => UserControllerFactory::class,

                UserDbMapper::class => UserDbMapperFactory::class,
                UserService::class => UserServiceFactory::class,

                UserFormManager::class => UserFormManagerFactory::class,

                AuthenticationListener::class => AuthenticationListenerFactory::class,

                Bootstrap::class => BootstrapFactory::class,
                AutoLogin::class => AutoLoginFactory::class,

                PasswordInterface::class => BcryptPasswordFactory::class,
                PasswordCheck::class => PasswordCheckFactory::class,

                UserFieldset::class => UserFieldsetFactory::class,
                UserInputFilter::class => UserInputFilterFactory::class,
            ],

            'aliases' => [
                'UserService' => UserService::class,
                'UserMapper' => UserDbMapper::class,
            ]
        ];
    }
}
