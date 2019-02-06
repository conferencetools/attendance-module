<?php
$viewDir = __DIR__ . '/../view/';
return [
    'attendance/layout' => $viewDir . 'layout.phtml',
    'attendance/admin-layout' => $viewDir . 'admin-layout.phtml',

    'attendance/purchase/index' => $viewDir . 'purchase/index.phtml',
    'attendance/purchase/delegates' => $viewDir . 'purchase/delegates.phtml',
    'attendance/purchase/payment' => $viewDir . 'purchase/payment.phtml',
    'attendance/purchase/complete' => $viewDir . 'purchase/complete.phtml',
    'attendance/delegate/update-details' => $viewDir . 'delegate/update-details.phtml',

    'attendance/admin/index/index' => $viewDir . 'admin/index/index.phtml',
    'attendance/admin/tickets/new-ticket' => $viewDir . 'admin/tickets/new-ticket.phtml',
    'attendance/admin/tickets/index' => $viewDir . 'admin/tickets/index.phtml',
    'attendance/admin/discounts/new-discount' => $viewDir . 'admin/discounts/new-discount.phtml',
    'attendance/admin/discounts/add-code' => $viewDir . 'admin/discounts/add-code.phtml',
    'attendance/admin/discounts/index' => $viewDir . 'admin/discounts/index.phtml',
    'attendance/admin/purchase/delegates' => $viewDir . 'admin/purchase/delegates.phtml',
    'attendance/admin/purchase/index' => $viewDir . 'admin/purchase/index.phtml',
    'attendance/admin/reports/index' => $viewDir . 'admin/reports/index.phtml',

    'email/receipt' => $viewDir . 'email/receipt.phtml',
];
