<?php
return [
    'dependencies' => [],

    'dot_user' => [
        'user_entity' => \Dot\User\Entity\UserEntity::class,

        'default_roles' => ['user'],

        'enable_account_confirmation' => true,
        'confirmed_account_status' => \Dot\User\Entity\UserEntity::STATUS_ACTIVE,

        'password_cost' => 11,

        'event_listeners' => [
            'user' => [

            ],
            'token' => [

            ],
            'controller' => [

            ]
        ],

        'login_options' => [
            'enable_remember' => true,
            'allowed_status' => [\Dot\User\Entity\UserEntity::STATUS_ACTIVE],
        ],
        'register_options' => [
            'enable_registration' => true,
            'default_user_status' => \Dot\User\Entity\UserEntity::STATUS_PENDING,

            'use_registration_captcha' => true,
            'captcha_options' => [],

            'login_after_registration' => false,
        ],
        'password_recovery_options' => [
            'enable_recovery' => true,
            'reset_token_timeout' => 3600,
        ],
        'template_options' => [
            'login_template' => '',
            'register_template' => '',
            'account_template' => '',
            'change_password_template' => '',
            'forgot_password_template' => '',
            'reset_password_template' => ''
        ],
        'messages_options' => [
            'messages' => [
                // checkout MessagesOptions class for a complete list of messages
            ]
        ]
    ]
];
