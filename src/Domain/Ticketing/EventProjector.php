<?php

namespace ConferenceTools\Attendance\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Ticketing\Event\EventCreated;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;

class EventProjector implements Handler
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(DomainMessage $message)
    {
        $event = $message->getMessage();
        switch (true) {
            case $event instanceof EventCreated:
                $this->eventCreated($event);
                break;
        }

        $this->repository->commit();
    }

    private function eventCreated(EventCreated $event)
    {
        $this->repository->add(
            new Event(
                $event->getId(),
                $event->getDescriptor(),
                $event->getCapacity(),
                $event->getStartsOn(),
                $event->getEndsOn()
            )
        );
    }

}