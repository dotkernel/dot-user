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
use Dot\User\Entity\UserEntity;
use Dot\User\Entity\UserEntityHydrator;
use Dot\User\Factory\AuthenticationListenerFactory;
use Dot\User\Factory\AutoLoginFactory;
use Dot\User\Factory\BootstrapFactory;
use Dot\User\Factory\Form\UserFormManagerFactory;
use Dot\User\Factory\PasswordDefaultFactory;
use Dot\User\Factory\UserControllerFactory;
use Dot\User\Factory\UserDbMapperFactory;
use Dot\User\Factory\UserOptionsFactory;
use Dot\User\Factory\UserServiceFactory;
use Dot\User\Form\UserFormManager;
use Dot\User\Listener\AuthenticationListener;
use Dot\User\Mapper\UserMapperInterface;
use Dot\User\Middleware\AutoLogin;
use Dot\User\Middleware\Bootstrap;
use Dot\User\Options\UserOptions;
use Dot\User\Service\PasswordInterface;
use Dot\User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\InvokableFactory;

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
        //check to see if we are in a zend expressive app and vendor folder
        //if so, install dk-user template path if exists in the root application, for template overwriting
        $addTemplatePath = false;
        $currentDir = getcwd();
        $templatePath = $currentDir . '/../../../../templates/dot-user';
        if(is_dir($templatePath)) {
            $addTemplatePath = true;
        }

        $config = [

            'dependencies' => [
                'factories' => [
                    UserOptions::class => UserOptionsFactory::class,

                    UserMapperInterface::class => UserDbMapperFactory::class,
                    UserServiceInterface::class => UserServiceFactory::class,

                    UserController::class => UserControllerFactory::class,

                    UserFormManager::class => UserFormManagerFactory::class,

                    UserEntity::class => InvokableFactory::class,
                    UserEntityHydrator::class => InvokableFactory::class,

                    AuthenticationListener::class => AuthenticationListenerFactory::class,

                    Bootstrap::class => BootstrapFactory::class,
                    AutoLogin::class => AutoLoginFactory::class,

                    PasswordInterface::class => PasswordDefaultFactory::class,
                ],

                'shared' => [
                    UserEntity::class => false,
                ],
            ],

            'middleware_pipeline' => [
                [
                    'middleware' => [
                        Bootstrap::class,
                        AutoLogin::class,
                    ],
                    'priority' => 10000,
                ]
            ],

            'dot_user' => [

                'user_listeners' => [],

                'db_options' => [],

                'register_options' => [],

                'login_options' => [],

                'password_recovery_options' => [],

                'confirm_account_options' => [],

                'messages_options' => [],

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
                    //we add the usual template path of a zend expressive app too
                    'dk-user' => [realpath(__DIR__ . '/../templates/dot-user')],
                ]
            ],
        ];

        if($addTemplatePath) {
            array_unshift($config['templates']['paths']['dot-user'], realpath($templatePath));
        }

        return $config;
    }
}