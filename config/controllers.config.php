<?php

use ConferenceTools\Attendance\Controller;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'factories' => [
        Controller\PurchaseController::class => InvokableFactory::class,
        Controller\DelegateController::class => InvokableFactory::class,
        Controller\Admin\TicketsController::class => InvokableFactory::class,
        Controller\Admin\ReportsController::class => InvokableFactory::class,
        Controller\Admin\IndexController::class =>  InvokableFactory::class,
        Controller\Admin\DiscountsController::class =>  InvokableFactory::class,
    ]
];
