<?php
$viewDir = __DIR__ . '/../view/';
return [
    'attendance/layout' => $viewDir . 'layout.phtml',
    'attendance/purchase/index' => $viewDir . 'purchase/index.phtml',
    'attendance/purchase/delegates' => $viewDir . 'purchase/delegates.phtml',
    'attendance/purchase/payment' => $viewDir . 'purchase/payment.phtml',
    'attendance/purchase/complete' => $viewDir . 'purchase/complete.phtml',
    'attendance/delegate/update-details' => $viewDir . 'delegate/update-details.phtml',
    'attendance/admin/tickets/new-ticket' => $viewDir . 'admin/tickets/new-ticket.phtml',
    'attendance/admin/tickets/index' => $viewDir . 'admin/tickets/index.phtml',
    'email/receipt' => $viewDir . 'email/receipt.phtml',
];