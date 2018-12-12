<?php
return [
    'driver' => [
        'ticketing' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [__DIR__ . '/../src/Domain/Ticketing']
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
        'reporting' => [
            'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
            'cache' => 'array',
            'paths' => [__DIR__ . '/../src/Domain/Reporting']
        ],
        'orm_default' => [
            'drivers' => [
                'ConferenceTools\\Attendance\\Domain\\Ticketing' => 'ticketing',
                'ConferenceTools\\Attendance\\Domain\\Purchasing' => 'purchasing',
                'ConferenceTools\\Attendance\\Domain\\Delegate' => 'delegates',
                'ConferenceTools\\Attendance\\Domain\\Reporting' => 'reporting',
            ]
        ]
    ],
];
