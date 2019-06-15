<?php

return [
    'view_manager' => [
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout.phtml',
            'error'  => __DIR__ . '/../view/error.phtml',
            '404' => __DIR__ . '/../view/404.phtml',
        ],
        'display_exceptions' => true,
    ],
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => Doctrine\DBAL\Driver\PDOSqlite\Driver::class,
                'params' => ['path'   => __DIR__ . '/../../_data/db.sqlite'],
            ]
        ]
    ],
    'mail' => [
        'type' => 'smtp',
        'options' => [
            'host' => 'smtp.gmail.com',
            'port' => 465,
            'connection_class' => 'plain',
            'connection_config' => [
                'username' => base64_decode('='),
                'password' => base64_decode('=='),
                'ssl' => 'ssl',
            ],
        ],
    ],
    'conferencetools' => [
        'mailconf' => [
            'purchase' => [
                'subject' => 'Your PHP Yorkshire Ticket Receipt',
                'from' => 'info@phpyorkshire.co.uk',
                'companyinfo' => 'PHP Yorkshire Ltd'
            ],
        ],
    ],
    'zfr_stripe' => [
        /**
         * Stripe SDK version to use
         */
        // 'version' => StripeClient::LATEST_API_VERSION
        'secret_key' => '',
        'publishable_key' => '',
    ]
];