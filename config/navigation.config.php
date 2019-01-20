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
    ],
];