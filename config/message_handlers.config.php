<?php

use ConferenceTools\Attendance\Domain\Ticketing;
use ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Delegate;
use ConferenceTools\Attendance\Domain\Reporting;
use ConferenceTools\Attendance\Factory;

return [
    'factories' => [
        Ticketing\AvailableTickets::class => Factory\Ticketing\AvailableTicketsFactory::class,
        Ticketing\Tickets::class => Factory\Ticketing\TicketsFactory::class,
        Purchasing\Projector::class => Factory\Purchasing\ProjectorFactory::class,
        Delegate\Projector::class => Factory\Delegate\ProjectorFactory::class,
        Reporting\CateringReport::class => Factory\Reporting\CateringReportFactory::class,
        \ConferenceTools\Attendance\Handler\StripePaymentHandler::class => \ConferenceTools\Attendance\Handler\StripePaymentHandlerFactory::class,
        \ConferenceTools\Attendance\Handler\EmailPurchase::class => \ConferenceTools\Attendance\Handler\EmailPurchaseFactory::class,
    ]
];