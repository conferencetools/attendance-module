<?php


namespace ConferenceTools\Attendance\Domain\Delegate;


class MessageSubscriptions
{
    public static function getSubscriptions(): array
    {
        return [
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
        ];
    }
}