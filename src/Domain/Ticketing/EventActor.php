<?php

namespace ConferenceTools\Attendance\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Ticketing\Command\CreateEvent;
use ConferenceTools\Attendance\Domain\Ticketing\Event\EventCreated;
use Phactor\Actor\AbstractActor;

class EventActor extends AbstractActor
{
    protected function handleCreateEvent(CreateEvent $command)
    {
        $this->fire(new EventCreated(
            $this->id(),
            $command->getDescriptor(),
            $command->getCapacity(),
            $command->getStartsOn(),
            $command->getEndsOn()
        ));
    }
}