<?php
return [
    'default' => [
        [
            'label' => 'Home',
            'route' => 'attendance-admin',
        ],
        [
            'label' => 'Discounts',
            'route' => 'attendance-admin/discounts',
            'permission' => 'discounts',
            'pages' => [
                [
                    'label' => 'Add',
                    'route' => 'attendance-admin/discounts/new',
                ],
            ],
        ],
        [
            'label' => 'Tickets',
            'route' => 'attendance-admin/tickets',
            'permission' => 'tickets',
            'pages' => [
                [
                    'label' => 'Add',
                    'route' => 'attendance-admin/tickets/new',
                ],
            ],
        ],
        [
            'label' => 'Create Purchase',
            'route' => 'attendance-admin/purchase',
            'permission' => 'orders',
        ],
        [
            'label' => 'Check in delegates',
            'route' => 'attendance-admin/checkin',
            'permission' => 'checkin',
        ],
        [
            'label' => 'Reporting',
            'route' => 'attendance-admin/reports',
            'permission' => 'reports',
            'pages' => [
                [
                    'label' => 'Catering preferences',
                    'route' => 'attendance-admin/reports/catering/preferences',
                ],
                [
                    'label' => 'Catering allergies',
                    'route' => 'attendance-admin/reports/catering/allergies',
                ],
                [
                    'label' => 'Delegates',
                    'route' => 'attendance-admin/reports/delegates',
                ],
                [
                    'label' => 'Purchases',
                    'route' => 'attendance-admin/reports/purchases',
                ],
                [
                    'label' => 'Sales',
                    'route' => 'attendance-admin/reports/sales',
                ],
            ],
        ],
    ],
];