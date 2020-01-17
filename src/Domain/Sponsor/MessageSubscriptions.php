<?php

namespace ConferenceTools\Attendance\Domain\Sponsor;

use Phactor\Message\MessageSubscriptionProvider;

class MessageSubscriptions implements MessageSubscriptionProvider
{
    public function getSubscriptions(): array
    {
        return [
            Command\CreateSponsor::class => [
                Projector::class,
            ]
        ];
    }
}