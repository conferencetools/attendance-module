<?php

use ConferenceTools\Attendance\Controller;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Placeholder;
use Zend\Router\Http\Segment;

//@TODO refactor routes
return [
    'attendance' => [
        'type' => Placeholder::class,
        'child_routes' => [
            'purchase' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/',
                    'defaults' => [
                        'controller' => Controller\PurchaseController::class,
                        'action'=> 'index'
                    ]
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'delegate-info' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => ':purchaseId/delegates',
                            'defaults' => [
                                'controller' => Controller\PurchaseController::class,
                                'action' => 'delegates',
                            ],
                        ]
                    ],
                    'payment' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => ':purchaseId/payment',
                            'defaults' => [
                                'controller' => Controller\PurchaseController::class,
                                'action' => 'payment',
                            ],
                        ]
                    ]
                ],
            ],
        ]
    ]
];
