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
                'may_terminate' => true,
                'child_routes' => [
                    'qrcode' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/qrcode',
                            'defaults' => [
                                'action' => 'qr-code',
                            ],
                        ],
                    ],
                    'resend-ticket' => [
                        'type' => Literal::class,
                        'options' => [
                            'route' => '/resend-ticket',
                            'defaults' => [
                                'action' => 'resend-ticket-email',
                            ],
                        ],
                    ],
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
    'may_terminate' => false,
    'options' => [
        'route' => '/admin',
        'defaults' => [
            'requiresAuth' => true,
            'layout' => 'admin/layout',
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
                'create' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/create',
                        'defaults' => [
                            'action' => 'create',
                        ],
                    ],
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
                'view' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/:purchaseId/view',
                        'defaults' => [
                            'action' => 'view',
                        ],
                    ],
                ],
                'payment-received' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/:purchaseId/payment-received/:paymentId',
                        'defaults' => [
                            'action' => 'payment-received',
                        ],
                    ],
                ],
            ]
        ],
        'checkin' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/checkin',
                'defaults'=> [
                    'controller' => Controller\Admin\CheckinController::class,
                    'action' => 'index',
                    'requiresPermission' => 'checkin',
                ]
            ],
            'may_terminate' => true,
            'child_routes' => [
                'checkin' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/:delegateId',
                        'defaults' => [
                            'action' => 'checkin'
                        ],
                    ],
                ],
            ],
        ],
        'reports' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/reports',
                'defaults'=> [
                    'controller' => Controller\Admin\ReportsController::class,
                    'requiresPermission' => 'reports',
                ]
            ],
            'may_terminate' => false,
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
                    'may_terminate' => true,
                    'child_routes' => [
                        'checkedIn' => [
                            'type' => Literal::class,
                            'options' => [
                                'route' => '/checked-in',
                                'defaults' => [
                                    'action' => 'checked-in-delegates',
                                ],
                            ],
                        ],
                    ],
                ],
                'purchases' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/purchases',
                        'defaults' => [
                            'action' => 'purchases',
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
        'merchandise' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/merchandise',
                'defaults' => [
                    'controller' => Controller\Admin\MerchandiseController::class,
                    'action' => 'index',
                    'requiresPermission' => 'merchandise',
                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'new' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/new',
                        'defaults' => [
                            'action' => 'new-merchandise'
                        ]
                    ],
                ],
                'withdraw' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/withdraw/:merchandiseId',
                        'defaults' => [
                            'action' => 'withdraw',
                        ]
                    ],
                ],
                'put-on-sale' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/put-on-sale/:merchandiseId',
                        'defaults' => [
                            'action' => 'put-on-sale',
                        ]
                    ],
                ],
            ],
        ],
        'events' => [
            'type' => Literal::class,
            'options' => [
                'route' => '/events',
                'defaults' => [
                    'controller' => Controller\Admin\EventsController::class,
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
                            'action' => 'new-event'
                        ]
                    ],
                ],
                'view' => [
                    'type' => Segment::class,
                    'options' => [
                        'route' => '/view/:eventId',
                        'defaults' => [
                            'action' => 'view',
                        ],
                    ],
                ],
            ],
        ],
        'delegate' => [
            'type' => Segment::class,
            'options' => [
                'route' => '/delegate/:delegateId',
                'defaults' => [
                    'controller' => Controller\Admin\DelegatesController::class,

                ],
            ],
            'may_terminate' => true,
            'child_routes' => [
                'resend-ticket' => [
                    'type' => Literal::class,
                    'options' => [
                        'route' => '/resend-ticket',
                        'defaults' => [
                            'action' => 'resend-ticket-email',

                        ],
                    ],
                ],
            ],
        ],
    ]
];

return $routes;