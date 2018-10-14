<?php

use ConferenceTools\Attendance\Controller;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'factories' => [
        Controller\PurchaseController::class => InvokableFactory::class,
    ]
];