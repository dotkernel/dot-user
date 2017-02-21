<?php
return [
    'dependencies' => [],

    'dot_user' => [
        'user_entity' => \Dot\User\Entity\UserEntity::class,

        'default_roles' => ['user'],

        'show_form_labels' => true,

        'enable_account_confirmation' => true,
        'confirmed_account_status' => \Dot\User\Entity\UserEntity::STATUS_ACTIVE,

        'password_cost' => 11,

        'event_listeners' => [
            'user' => [

            ],

            'token' => [

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
            // change default template file references or the base layout(for example)
            'register_template' => 'your-app::register_template',
            'register_template_layout' => 'your-app::layout',
            // for a full list see documentation, or check out TemplateOptions class
        ],
        'messages_options' => [
            'messages' => [
                // checkout MessagesOptions class for a complete list of messages
            ]
        ]
    ]
];
