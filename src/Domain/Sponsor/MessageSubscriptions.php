<?php

namespace ConferenceTools\Attendance\Domain\Sponsor;

use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateListCreated;
use Phactor\Message\MessageSubscriptionProvider;

class MessageSubscriptions implements MessageSubscriptionProvider
{
    public function getSubscriptions(): array
    {
        return [
            Command\CreateSponsor::class => [
                Projector::class,
            ],
            Command\AddQuestion::class => [
                Projector::class,
            ],
            Command\DeleteQuestion::class => [
                Projector::class,
            ],

            DelegateListCreated::class => [
                Projector::class,
            ],
        ];
    }
}