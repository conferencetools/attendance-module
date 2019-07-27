<?php
return [
    'driver' => [
        'ticketing' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [__DIR__ . '/../src/Domain/Ticketing']
        ],
        'discounting' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [__DIR__ . '/../src/Domain/Discounting']
        ],
        'payments' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [__DIR__ . '/../src/Domain/Payment']
        ],
        'purchasing' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [__DIR__ . '/../src/Domain/Purchasing']
        ],
        'delegates' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [__DIR__ . '/../src/Domain/Delegate']
        ],
        'orm_default' => [
            'drivers' => [
                'ConferenceTools\\Attendance\\Domain\\Ticketing' => 'ticketing',
                'ConferenceTools\\Attendance\\Domain\\Discounting' => 'discounting',
                'ConferenceTools\\Attendance\\Domain\\Payment' => 'payments',
                'ConferenceTools\\Attendance\\Domain\\Purchasing' => 'purchasing',
                'ConferenceTools\\Attendance\\Domain\\Delegate' => 'delegates',
            ]
        ]
    ],
];
