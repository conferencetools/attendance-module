<?php
$subscriptions = [
    \ConferenceTools\Attendance\Domain\Payment\Command\TakePayment::class => [
        \ConferenceTools\Attendance\Handler\StripePaymentHandler::class,
    ]
];

return Zend\Stdlib\ArrayUtils::merge(
    \ConferenceTools\Attendance\Domain\MessageSubscriptions::getSubscriptions(),
    $subscriptions
);