<?php
/**
 * @copyright: DotKernel
 * @library: dk-user
 * @author: n3vra
 * Date: 1/27/2017
 * Time: 2:39 PM
 */

namespace Dot\User;

use Dot\Ems\Factory\DbMapperFactory;
use Dot\User\Authentication\AuthenticationListener;
use Dot\User\Authentication\InjectLoginForm;
use Dot\User\Controller\UserController;
use Dot\User\Entity\ConfirmTokenEntity;
use Dot\User\Entity\RememberTokenEntity;
use Dot\User\Entity\ResetTokenEntity;
use Dot\User\Entity\RoleEntity;
use Dot\User\Entity\UserEntity;
use Dot\User\Factory\AuthenticationListenerFactory;
use Dot\User\Factory\AutoLoginFactory;
use Dot\User\Factory\BcryptFactory;
use Dot\User\Factory\FormFactory;
use Dot\User\Factory\InjectLoginFormFactory;
use Dot\User\Factory\PasswordCheckFactory;
use Dot\User\Factory\TokenServiceFactory;
use Dot\User\Factory\UserControllerFactory;
use Dot\User\Factory\UserDbMapperFactory;
use Dot\User\Factory\UserFieldsetFactory;
use Dot\User\Factory\UserOptionsFactory;
use Dot\User\Factory\UserServiceFactory;
use Dot\User\Form\AccountForm;
use Dot\User\Form\ChangePasswordForm;
use Dot\User\Form\ForgotPasswordForm;
use Dot\User\Form\LoginForm;
use Dot\User\Form\RegisterForm;
use Dot\User\Form\ResetPasswordForm;
use Dot\User\Form\UserFieldset;
use Dot\User\Mapper\RoleDbMapper;
use Dot\User\Mapper\TokenDbMapper;
use Dot\User\Mapper\UserDbMapper;
use Dot\User\Middleware\AutoLogin;
use Dot\User\Options\UserOptions;
use Dot\User\Service\PasswordCheck;
use Dot\User\Service\TokenService;
use Dot\User\Service\TokenServiceInterface;
use Dot\User\Service\UserService;
use Dot\User\Service\UserServiceInterface;
use Zend\Crypt\Password\PasswordInterface;

/**
 * Class ConfigProvider
 * @package Dot\User
 */
class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependenciesConfig(),

            'middleware_pipeline' => [
                [
                    'middleware' => AutoLogin::class,
                    'priority' => 9999,
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
                    'middleware' => UserController::class,
                ],
            ],

            'templates' => [
                'paths' => [
                    'dot-user-form' => [realpath(__DIR__ . '/../templates/dot-user-form')],
                ],
            ],

            'dot_authentication' => [
                'web' => [
                    'login_route' => ['route_name' => 'login'],
                    'logout_route' => ['route_name' => 'logout'],

                    'after_login_route' => ['route_name' => 'user', 'route_params' => ['action' => 'account']],
                    'after_logout_route' => ['route_name' => 'login'],

                    'event_listeners' => [
                        [
                            'type' => InjectLoginForm::class,
                            'priority' => 600
                        ],
                        [
                            'type' => AuthenticationListener::class,
                            'priority' => 500
                        ],
                    ],
                ]
            ],

            'dot_user' => [
                'login_options' => [],
                'messages_options' => [
                    'messages' => []
                ],
                'password_recovery_options' => [],
                'register_options' => [],
                'template_options' => [],
            ],

            'dot_ems' => [
                'default_adapter' => 'database',

                'mapper_manager' => [
                    'factories' => [
                        RoleDbMapper::class => DbMapperFactory::class,
                        UserDbMapper::class => UserDbMapperFactory::class,
                        TokenDbMapper::class => DbMapperFactory::class,
                    ],
                    'aliases' => [
                        RoleEntity::class => RoleDbMapper::class,
                        UserEntity::class => UserDbMapper::class,

                        ConfirmTokenEntity::class => TokenDbMapper::class,
                        RememberTokenEntity::class => TokenDbMapper::class,
                        ResetTokenEntity::class => TokenDbMapper::class,
                    ]
                ],
            ],

            'dot_form' => [
                'form_manager' => [
                    'factories' => [
                        UserFieldset::class => UserFieldsetFactory::class,
                        RegisterForm::class => FormFactory::class,
                        AccountForm::class => FormFactory::class,
                        ChangePasswordForm::class => FormFactory::class,
                        ForgotPasswordForm::class => FormFactory::class,
                        LoginForm::class => FormFactory::class,
                        ResetPasswordForm::class => FormFactory::class,
                    ],
                    'aliases' => [
                        'UserFieldset' => UserFieldset::class,
                        'Register' => RegisterForm::class,
                        'Account' => AccountForm::class,
                        'ChangePassword' => ChangePasswordForm::class,
                        'ForgotPassword' => ForgotPasswordForm::class,
                        'Login' => LoginForm::class,
                        'ResetPassword' => ResetPasswordForm::class,
                    ]
                ]
            ]
        ];
    }

    public function getDependenciesConfig()
    {
        return [
            'factories' => [
                PasswordInterface::class => BcryptFactory::class,
                PasswordCheck::class => PasswordCheckFactory::class,

                UserOptions::class => UserOptionsFactory::class,
                UserController::class => UserControllerFactory::class,
                UserService::class => UserServiceFactory::class,
                TokenService::class => TokenServiceFactory::class,

                AutoLogin::class => AutoLoginFactory::class,
                InjectLoginForm::class => InjectLoginFormFactory::class,
                AuthenticationListener::class => AuthenticationListenerFactory::class,
            ],
            'aliases' => [
                UserServiceInterface::class => UserService::class,
                'UserService' => UserServiceInterface::class,
                TokenServiceInterface::class => TokenService::class,
                'TokenService' => TokenServiceInterface::class,
            ]
        ];
    }
}
