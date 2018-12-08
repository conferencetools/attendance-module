<?php

use ConferenceTools\Attendance\Controller;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Placeholder;
use Zend\Router\Http\Segment;

//@TODO refactor routes
$routes = [
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
                    ],
                    'complete' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => ':purchaseId/complete',
                            'defaults' => [
                                'controller' => Controller\PurchaseController::class,
                                'action' => 'complete',
                            ],
                        ]
                    ],
                ],
            ],
            'delegates' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/delegate/:delegateId',
                    'defaults' => [
                        'controller' => Controller\DelegateController::class,
                        'action' => 'update-details',
                    ]
                ],
            ],
        ]
    ],

];
if (!function_exists('getflag')) {
    function getflag(string $name, bool $default = false): bool
    {
        $value = getenv($name);
        return $value ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : $default;
    }
}

if (getflag('ENABLE_ADMIN')) {
    $routes['attendance-admin'] = [
        'type' => Literal::class,
        'may_terminate' => true,
        'options' => [
            'route' => '/admin',
        ],
        'child_routes' => [
            'tickets' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/tickets',
                    'defaults' => [
                        'controller' => Controller\Admin\TicketsController::class,
                        'action' => 'index'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'new' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/new',
                            'defaults' => [
                                'action' => 'new-ticket'
                            ]
                        ],
                    ]
                ],

            ]
        ]
    ];
}

return $routes;