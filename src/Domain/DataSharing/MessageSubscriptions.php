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
            ],

            Event\DelegateListCreated::class => [
                Projector::class,
            ],
            Event\DelegateAdded::class => [
                Projector::class,
            ],
            Event\DelegateUpdated::class => [
                Projector::class,
            ],
            Event\LastCollectionTimeSet::class => [
                Projector::class,
            ],
            Event\ListAvailableTimeSet::class => [
                Projector::class,
            ],
            Event\ListAvailable::class => [
                Projector::class,
            ],
            Event\CollectionTerminated::class => [
                Projector::class,
            ],
        ];
    }
}