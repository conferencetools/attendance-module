<?php


namespace ConferenceTools\Attendance\Domain\Payment;


use ConferenceTools\Attendance\Domain\Payment\Command;
use ConferenceTools\Attendance\Domain\Payment\Event;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCheckedOut;

class MessageSubscriptions
{
    public static function getSubscriptions(): array
    {
        return [
            // ######## external events ########
            PurchaseCheckedOut::class => [
                Payment::class,
            ],

            // ######## payment commands ########
            Command\CheckPaymentTimeout::class => [
                Payment::class,
            ],
            Command\ConfirmPayment::class => [
                Payment::class,
            ],
            Command\ProvidePaymentDetails::class => [
                Payment::class,
            ],
            Command\SelectPaymentMethod::class => [
                Payment::class,
            ],

            // ######## payment events ########
            Event\PaymentConfirmed::class => [
                Projector::class,
            ],
            Event\PaymentMethodSelected::class => [
                Projector::class,
            ],
            Event\PaymentPending::class => [
                Projector::class,
            ],
            Event\PaymentRaised::class => [
                Projector::class,
            ],
            Event\PaymentStarted::class => [
                Projector::class,
            ],
            Event\PaymentTimedOut::class => [
                Projector::class,
            ],
        ];
    }
}