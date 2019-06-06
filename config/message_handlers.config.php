<?php

use ConferenceTools\Attendance\Domain\Ticketing;
use ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Delegate;
use ConferenceTools\Attendance\Domain\Discounting;
use ConferenceTools\Attendance\Factory;

return [
    'factories' => [
        Ticketing\TicketProjector::class => Factory\Ticketing\TicketsFactory::class,
        Ticketing\EventProjector::class => Factory\Ticketing\EventProjectorFactory::class,
        Purchasing\Projector::class => Factory\Purchasing\ProjectorFactory::class,
        Delegate\Projector::class => Factory\Delegate\ProjectorFactory::class,
        Discounting\Projector::class => Factory\Discounting\ProjectorFactory::class,
        \ConferenceTools\Attendance\Handler\StripePaymentHandler::class => \ConferenceTools\Attendance\Handler\StripePaymentHandlerFactory::class,
        \ConferenceTools\Attendance\Handler\EmailPurchase::class => \ConferenceTools\Attendance\Handler\EmailPurchaseFactory::class,
        \ConferenceTools\Attendance\Handler\EmailTicket::class => \ConferenceTools\Attendance\Handler\EmailTicketFactory::class,
    ]
];