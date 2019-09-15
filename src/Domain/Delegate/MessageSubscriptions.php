<?php


namespace ConferenceTools\Attendance\Domain\Delegate;

use ConferenceTools\Attendance\Domain\Purchasing;

class MessageSubscriptions
{
    public static function getSubscriptions(): array
    {
        return [
            Purchasing\Event\PurchaseCompleted::class => [
                Projector::class,
            ],

            Command\RegisterDelegate::class => [
                Delegate::class,
            ],
            Command\UpdateDelegateDetails::class => [
                Delegate::class,
            ],
            Command\CheckIn::class => [
                Delegate::class,
            ],

            Event\DelegateRegistered::class => [
                Projector::class,
            ],
            Event\DelegateDetailsUpdated::class => [
                Projector::class,
            ],
            Event\CheckedIn::class => [
                Projector::class,
            ],
            Event\CheckinIdGenerated::class => [
                Projector::class,
            ],
        ];
    }
}