<?php
return [
    \ConferenceTools\Attendance\Domain\Payment\Command\TakePayment::class => [
        \ConferenceTools\Attendance\Handler\StripePaymentHandler::class,
    ],
    \ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade::class => [
        \ConferenceTools\Attendance\Handler\EmailPurchase::class,
    ],
];
