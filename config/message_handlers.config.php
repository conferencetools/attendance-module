<?php

use ConferenceTools\Attendance\Domain\{Ticketing, Purchasing, Delegate, Discounting, Payment, Merchandise, DataSharing, Sponsor};
use ConferenceTools\Attendance\Factory;

return [
    'factories' => [
        Ticketing\TicketProjector::class => Factory\Ticketing\TicketsFactory::class,
        Ticketing\EventProjector::class => Factory\Ticketing\EventProjectorFactory::class,
        Purchasing\Projector::class => Factory\Purchasing\ProjectorFactory::class,
        Delegate\Projector::class => Factory\Delegate\ProjectorFactory::class,
        Discounting\Projector::class => Factory\Discounting\ProjectorFactory::class,
        Payment\Projector::class => Factory\Payment\ProjectorFactory::class,
        Payment\ZeroPaymentHandler::class => Factory\Payment\ZeroPaymentHandlerFactory::class,
        Merchandise\MerchandiseProjector::class => Factory\Merchandise\ProjectorFactory::class,
        \ConferenceTools\Attendance\Handler\EmailPurchase::class => \ConferenceTools\Attendance\Handler\EmailPurchaseFactory::class,
        \ConferenceTools\Attendance\Handler\EmailTicket::class => \ConferenceTools\Attendance\Handler\EmailTicketFactory::class,
        DataSharing\Projector::class => Factory\DataSharing\ProjectorFactory::class,
        DataSharing\NotifiesDelegates::class => Factory\DataSharing\NotifiesDelegatesFactory::class,
        Sponsor\Projector::class => Factory\Sponsor\ProjectorFactory::class,
    ]
];