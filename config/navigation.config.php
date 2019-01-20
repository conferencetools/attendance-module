<?php
return [
    'default' => [
        [
            'label' => 'Home',
            'route' => 'attendance-admin',
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