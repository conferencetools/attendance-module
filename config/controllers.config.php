<?php

use ConferenceTools\Attendance\Controller;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'factories' => [
        Controller\PurchaseController::class => Controller\PurchaseControllerFactory::class,
        Controller\DelegateController::class => InvokableFactory::class,
        Controller\Admin\TicketsController::class => InvokableFactory::class,
        Controller\Admin\ReportsController::class => InvokableFactory::class,
        Controller\Admin\IndexController::class =>  InvokableFactory::class,
        Controller\Admin\DiscountsController::class =>  InvokableFactory::class,
        Controller\Admin\CheckinController::class =>  InvokableFactory::class,
        Controller\Admin\EventsController::class =>  InvokableFactory::class,
        Controller\Admin\PurchaseController::class =>  InvokableFactory::class,
        Controller\Admin\MerchandiseController::class => InvokableFactory::class,
    ]
];
