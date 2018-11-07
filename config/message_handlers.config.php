<?php

use ConferenceTools\Attendance\Domain\Ticketing;
use ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Delegate;
use ConferenceTools\Attendance\Factory;

return [
    'factories' => [
        Ticketing\AvailableTickets::class => Factory\Ticketing\AvailableTicketsFactory::class,
        Purchasing\Projector::class => Factory\Purchasing\ProjectorFactory::class,
        Delegate\Projector::class => Factory\Delegate\ProjectorFactory::class,
        \ConferenceTools\Attendance\Handler\StripePaymentHandler::class => \ConferenceTools\Attendance\Handler\StripePaymentHandlerFactory::class,
    ]
];