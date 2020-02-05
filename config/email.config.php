<?php
return [
    '_defaults' => [
        'from' => '',
        'companyinfo' => 'Your company',
    ],
    'ticket' => [
        'subject' => 'Your ticket',
        'from' => null,
        'template' => 'email/ticket',
        'companyinfo' => null,
    ],
    'purchase' => [
        'subject' => 'Your receipt',
        'from' => null,
        'template' => 'email/receipt',
        'companyinfo' => null,
    ],
    'delegate-data-notification' => [
        'subject' => 'Your receipt',
        'from' => null,
        'template' => 'email/delegate-data-notification',
        'companyinfo' => null,
    ],
];