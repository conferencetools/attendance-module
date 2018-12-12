<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;


use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;

class Tickets implements Handler
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
            case $event instanceof TicketsReleased:
                $this->newTicket($event);
                break;
        }
    }

    private function newTicket(TicketsReleased $event)
    {
        $entity = new Ticket($event->getId(), $event->getEvent(), $event->getQuantity(), $event->getPrice(), $event->getAvailabilityDates(), $event->isPrivate());
        $this->repository->add($entity);
    }
}