<?php

namespace ConferenceTools\Attendance\Domain\Purchasing;

use Carnage\Phactor\Actor\AbstractActor;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\CheckPurchaseTimeout;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseTickets;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchasePaid;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketAllocatedToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;

class Purchase extends AbstractActor
{
    private $unallocatedTickets;
    private $paid;
    private $tickets;

    protected function handlePurchaseTickets(PurchaseTickets $command)
    {
        foreach ($command->getTickets() as $ticket) {
            /** @var TicketQuantity $ticket */
            $this->fire(new TicketsReserved($this->id(), $ticket->getTicketId(), $ticket->getQuantity()));
        }
        $this->schedule(new CheckPurchaseTimeout($this->id()), (new \DateTime())->add(new \DateInterval('PT1800S')));
    }

    protected function applyTicketsReserved(TicketsReserved $event)
    {
        $this->unallocatedTickets[$event->getTicketId()] = $event->getQuantity();
        $this->tickets[$event->getTicketId()] = $event->getQuantity();
    }

    protected function handleAllocateTicketToDelegate(AllocateTicketToDelegate $command)
    {
        if (isset($this->unallocatedTickets[$command->getTicketId()]) && $this->unallocatedTickets[$command->getTicketId()] > 0) {
            $this->fire(new TicketAllocatedToDelegate($this->id(), $command->getTicketId(), $command->getDelegateId()));
        }
    }

    protected function applyTicketAllocatedToDelegate(TicketAllocatedToDelegate $event)
    {
        $this->unallocatedTickets[$event->getTicketId()]--;
    }

    protected function handleCheckPurchaseTimeout(CheckPurchaseTimeout $command)
    {
        if (!$this->paid) {
            foreach ($this->tickets as $ticketId => $quantity) {
                $this->fire(new TicketReservationExpired($this->id(), $ticketId, $quantity));
            }

            //@TODO deallocate any tickets from delegates (maybe leave them in place for now; at some point there needs
            // to be a full delete of unused delegates and the related purchases (GDPR) leaving the tickets allocated
            // makes it easier to add a revive expired purchase option further down the line
        }
    }

    protected function applyPurchasePaid(PurchasePaid $event)
    {
        $this->paid = true;
    }
}