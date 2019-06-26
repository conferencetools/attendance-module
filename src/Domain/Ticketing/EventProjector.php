<?php

namespace ConferenceTools\Attendance\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Ticketing\Event\EventCreated;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket as TicketReadModel;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;

class EventProjector implements Handler
{
    private $eventRepository;
    private $ticketRepository;

    public function __construct(Repository $eventRepository, Repository $ticketRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->ticketRepository = $ticketRepository;
    }

    public function handle(DomainMessage $message)
    {
        $event = $message->getMessage();
        switch (true) {
            case $event instanceof EventCreated:
                $this->eventCreated($event);
                break;
            case $event instanceof TicketsReserved:
                $this->ticketsReserved($event);
                break;
            case $event instanceof TicketReservationExpired:
                $this->ticketsTicketReservationExpired($event);
                break;
        }

        $this->eventRepository->commit();
    }

    private function eventCreated(EventCreated $event): void
    {
        $this->eventRepository->add(
            new Event(
                $event->getId(),
                $event->getDescriptor(),
                $event->getCapacity(),
                $event->getStartsOn(),
                $event->getEndsOn()
            )
        );
    }

    private function ticketsReserved(TicketsReserved $event): void
    {
        /** @var TicketReadModel $ticket */
        $ticket = $this->ticketRepository->get($event->getTicketId());
        /** @var Event $eventReadModel */
        $eventReadModel = $this->eventRepository->get($ticket->getEventId());
        $eventReadModel->increaseRegistered($event->getQuantity());
    }

    private function ticketsTicketReservationExpired(TicketReservationExpired $event): void
    {
        /** @var TicketReadModel $ticket */
        $ticket = $this->ticketRepository->get($event->getTicketId());
        /** @var Event $eventReadModel */
        $eventReadModel = $this->eventRepository->get($ticket->getEventId());
        $eventReadModel->decreaseRegistered($event->getQuantity());
    }

}