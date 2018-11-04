<?php
return [
    \ConferenceTools\Attendance\Domain\Payment\Command\TakePayment::class => [
        \ConferenceTools\Attendance\Handler\StripePaymentHandler::class,
    ]
];
