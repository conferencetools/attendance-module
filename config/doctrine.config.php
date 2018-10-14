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
        'orm_default' => [
            'drivers' => [
                'ConferenceTools\\Attendance\\Domain\\Ticketing' => 'ticketing',
                'ConferenceTools\\Attendance\\Domain\\Purchasing' => 'purchasing'
            ]
        ]
    ],
];
