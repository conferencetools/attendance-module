<?php
return [
    \ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCompleted::class => [
        \ConferenceTools\Attendance\Handler\EmailPurchase::class,
    ],
    \ConferenceTools\Attendance\Domain\Delegate\Event\CheckinIdGenerated::class => [
        \ConferenceTools\Attendance\Handler\EmailTicket::class,
    ],
];
