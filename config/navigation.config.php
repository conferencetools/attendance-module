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
        ],
        [
            'label' => 'Reporting',
            'route' => 'attendance-admin/reports',
            'pages' => [
                [
                    'label' => 'Catering preferences',
                    'route' => 'attendance-admin/reports/catering/preferences',
                ],
                [
                    'label' => 'Catering allergies',
                    'route' => 'attendance-admin/reports/catering/allergies',
                ],
            ],
        ],
    ],
];