<?php


namespace ConferenceTools\Attendance\Domain\DataSharing;


use Phactor\Message\MessageSubscriptionProvider;

class MessageSubscriptions implements MessageSubscriptionProvider
{
    public function getSubscriptions(): array
    {
        return [
            Command\CreateDelegateList::class => [
                DelegateList::class,
            ],
            Command\AddDelegate::class => [
                DelegateList::class,
            ],
            Command\SetListAvailableTime::class => [
                DelegateList::class,
            ],
            Command\SetLastCollectionTime::class => [
                DelegateList::class,
            ],
            Command\MakeListAvailable::class => [
                DelegateList::class,
            ],
            Command\TerminateCollection::class => [
                DelegateList::class,
            ]
        ];
    }
}