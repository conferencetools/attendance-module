<?php
return [
    \ConferenceTools\Attendance\Domain\Payment\Command\TakePayment::class => [
        \ConferenceTools\Attendance\Handler\StripePaymentHandler::class,
    ],
    \ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade::class => [
        \ConferenceTools\Attendance\Handler\EmailPurchase::class,
    ],
    \ConferenceTools\Attendance\Domain\Delegate\Command\SendTicketEmail::class => [
        \ConferenceTools\Attendance\Handler\EmailTicket::class,
    ],
    \ConferenceTools\Attendance\Domain\Payment\Event\PaymentMethodSelected::class => [
        \ConferenceTools\Attendance\PaymentProvider\StripePaymentHandler::class,
    ],
    \ConferenceTools\Attendance\PaymentProvider\Webhook\CreateWebhook::class => [
        \ConferenceTools\Attendance\PaymentProvider\Webhook\CreateWebhookHandler::class,
    ],
];
