<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;


use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\TicketsForSale;

class AvailableTickets implements Handler
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
            case $event instanceof TicketsOnSale:
                $this->newTicket($event);
                break;
            case $event instanceof TicketsWithdrawnFromSale:
                $this->removeTicket($event);
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

    private function newTicket(TicketsOnSale $message)
    {
        $entity = new TicketsForSale($message->getId(), $message->getTicket(), $message->getQuantity(), $message->getPrice());
        $this->repository->add($entity);
    }

    private function removeTicket(TicketsWithdrawnFromSale $message)
    {
        $entity = $this->repository->get($message->getId());
        $this->repository->remove($entity);
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
        $entity->increaseRemainingBy($message->getQuantity());
    }
}