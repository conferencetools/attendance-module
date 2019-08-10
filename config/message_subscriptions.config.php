<?php
return [
    \ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade::class => [
        \ConferenceTools\Attendance\Handler\EmailPurchase::class,
    ],
    \ConferenceTools\Attendance\Domain\Delegate\Command\SendTicketEmail::class => [
        \ConferenceTools\Attendance\Handler\EmailTicket::class,
    ],
];
