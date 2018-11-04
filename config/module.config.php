<?php

return [
    'asset_manager' => require __DIR__ . '/asset.config.php',
    'controllers' => require __DIR__ . '/controllers.config.php',
    'doctrine' => require __DIR__ . '/doctrine.config.php',
    'message_handlers' => require __DIR__ . '/message_handlers.config.php',
    'message_subscriptions' => require __DIR__ . '/message_subscriptions.config.php',
    'message_subscription_providers' => [\ConferenceTools\Attendance\Domain\MessageSubscriptions::class],
    'router' => [
        'routes' => require __DIR__ . '/routes.config.php',
    ],
    'view_manager' => [
        'controller_map' => [
            'ConferenceTools\Attendance\Controller' => 'attendance',
        ],
        'template_map' => require __DIR__ . '/views.config.php',
    ],
    'view_helpers' => [
        'invokables' => [
            //'flashMessenger' => \ConferenceTools\Tickets\View\Helper\FlashMessenger::class,
            'moneyFormat' => \ConferenceTools\Attendance\View\Helper\MoneyFormat::class,
        ],
        'factories' => [
            'stripeKey' => \ConferenceTools\Attendance\View\Helper\StripeKeyFactory::class,
            //'ticketsConfig' => \ConferenceTools\Tickets\View\Helper\ConfigurationFactory::class,
            //'serverUrl' => \ConferenceTools\Tickets\View\Helper\ServerUrlFactory::class,
        ],
    ],
];