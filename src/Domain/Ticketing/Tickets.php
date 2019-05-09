<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;


use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;
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
            case $event instanceof TicketsWithdrawnFromSale:
                $this->withdrawn($event);
                break;
            case $event instanceof TicketsOnSale:
                $this->onSale($event);
                break;
            case $event instanceof TicketsReserved:
                $this->ticketsReserved($event);
                break;
            case $event instanceof TicketReservationExpired:
                $this->ticketsExpired($event);
                break;
        }

        $this->repository->commit();
    }

    private function newTicket(TicketsReleased $event)
    {
        $entity = new Ticket($event->getId(), $event->getEventId(), $event->getDescriptor(), $event->getQuantity(), $event->getPrice(), $event->getAvailabilityDates());
        $this->repository->add($entity);
    }

    private function withdrawn(TicketsWithdrawnFromSale $event)
    {
        /** @var Ticket $ticket */
        $ticket = $this->repository->get($event->getId());
        $ticket->withdraw();
    }

    private function onSale(TicketsOnSale $event)
    {
        /** @var Ticket $ticket */
        $ticket = $this->repository->get($event->getId());
        $ticket->onSale();
    }

    private function ticketsReserved(TicketsReserved $message)
    {
        /** @var Ticket $entity */
        $entity = $this->repository->get($message->getTicketId());
        $entity->decreaseRemainingBy($message->getQuantity());
    }

    private function ticketsExpired(TicketReservationExpired $message)
    {
        /** @var Ticket $entity */
        $entity = $this->repository->get($message->getTicketId());
        if ($entity !== null) {
            $entity->increaseRemainingBy($message->getQuantity());
        }
    }
}