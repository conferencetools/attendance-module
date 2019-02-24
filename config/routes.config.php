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

$routes['attendance-admin'] = [
    'type' => Literal::class,
    'may_terminate' => true,
    'options' => [
        'route' => '/admin',
        'defaults' => [
            'requiresAuth' => true,
            'layout' => 'attendance/admin-layout',
            'controller' => Controller\Admin\IndexController::class,
            'action' => 'index'
        ]
    ],
    'child_routes' => [
        'purchase' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/purchase',
                'defaults'=> [
                    'controller' => Controller\Admin\PurchaseController::class,
                    'action' => 'index',
                    'requiresPermission' => 'orders',
                ]
            ],
            'may_terminate' => true,
            'child_routes' => [
                'delegates' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/delegates',
                        'defaults' => [
                            'action' => 'delegates',
                        ],
                    ],
                ]
            ]
        ],
        'reports' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/reports',
                'defaults'=> [
                    'controller' => Controller\Admin\ReportsController::class,
                    'action' => 'index',
                    'requiresPermission' => 'reports',
                ]
            ],
            'may_terminate' => true,
            'child_routes' => [
                'catering' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/catering',
                    ],
                    'child_routes' => [
                        'preferences' => [
                            'type' => Literal::class,
                            'options' => [
                                'route' => '/preferences',
                                'defaults' => [
                                    'action' => 'cateringPreferences',
                                ]
                            ],
                        ],
                        'allergies' => [
                            'type' => Literal::class,
                            'options' => [
                                'route' => '/allergies',
                                'defaults' => [
                                    'action' => 'cateringAlergies',
                                ]
                            ],
                        ],
                    ]
                ],
                'delegates' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/delegates',
                        'defaults' => [
                            'action' => 'delegates',
                        ],
                    ],
                ],
                'sales' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/sales',
                        'defaults' => [
                            'action' => 'sales',
                        ],
                    ],
                ],
            ]
        ],
        'tickets' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/tickets',
                'defaults' => [
                    'controller' => Controller\Admin\TicketsController::class,
                    'action' => 'index',
                    'requiresPermission' => 'tickets',
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
                ],
                'withdraw' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/withdraw/:ticketId',
                        'defaults' => [
                            'action' => 'withdraw',
                        ]
                    ],
                ],
                'put-on-sale' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/put-on-sale/:ticketId',
                        'defaults' => [
                            'action' => 'put-on-sale',
                        ]
                    ],
                ],
            ],

        ],
        'discounts' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/discounts',
                'defaults' => [
                    'controller' => Controller\Admin\DiscountsController::class,
                    'action' => 'index',
                    'requiresPermission' => 'discounts',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'new' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/new',
                        'defaults' => [
                            'action' => 'new-discount'
                        ]
                    ],
                ],
                'add-code' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/:discountId/add-code',
                        'defaults' => [
                            'action' => 'add-code'
                        ]
                    ],
                ],
            ],
        ],
    ]
];

return $routes;