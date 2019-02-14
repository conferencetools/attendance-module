<?php

namespace ConferenceTools\Attendance\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Purchasing\Command\ApplyDiscount;
use ConferenceTools\Attendance\Domain\Purchasing\Event\DiscountApplied;
use Phactor\Actor\AbstractActor;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\CheckPurchaseTimeout;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseTickets;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;
use ConferenceTools\Attendance\Domain\Purchasing\Event\OutstandingPaymentCalculated;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseStartedBy;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketAllocatedToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Ticketing\Price;

class Purchase extends AbstractActor
{
    private $unallocatedTickets;
    private $paid;
    /** @var TicketQuantity[] */
    private $tickets;
    private $email;
    /** @var Price */
    private $total;

    protected function handlePurchaseTickets(PurchaseTickets $command)
    {
        $this->fire(new PurchaseStartedBy($this->id(), $command->getEmail(), $command->getDelegates()));
        /** @var Price $total */
        $total = null;

        foreach ($command->getTickets() as $ticket) {
            /** @var TicketQuantity $ticket */
            $this->fire(new TicketsReserved($this->id(), $ticket->getTicketId(), $ticket->getQuantity()));
            $total = ($total === null) ? $ticket->getTotalPrice() : $total->add($ticket->getTotalPrice());
        }

        $this->fire(new OutstandingPaymentCalculated($this->id(), $total));

        $this->schedule(new CheckPurchaseTimeout($this->id()), (new \DateTime())->add(new \DateInterval('PT1800S')));
    }

    protected function applyPurchaseTickets(PurchaseTickets $command)
    {
        $this->tickets = $command->getTickets();
    }

    protected function handleApplyDiscount(ApplyDiscount $command)
    {
        $totalDiscount = $command->getDiscount()->calculateDiscount(...$this->tickets);
        $this->fire(new DiscountApplied($this->id(), $command->getDiscountId(), $command->getDiscountCode()));
        $this->fire(new OutstandingPaymentCalculated($this->id(), $this->total->subtract($totalDiscount)));
    }

    protected function applyPurchaseStartedBy(PurchaseStartedBy $event)
    {
        $this->email = $event->getEmail();
    }

    protected function applyTicketsReserved(TicketsReserved $event)
    {
        $this->unallocatedTickets[$event->getTicketId()] = $event->getQuantity();
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
            foreach ($this->tickets as $ticket) {
                $this->fire(new TicketReservationExpired($this->id(), $ticket->getTicketId(), $ticket->getQuantity()));
            }

            //@TODO deallocate any tickets from delegates (maybe leave them in place for now; at some point there needs
            // to be a full delete of unused delegates and the related purchases (GDPR) leaving the tickets allocated
            // makes it easier to add a revive expired purchase option further down the line
        }
    }

    protected function applyPaymentMade(PaymentMade $event)
    {
        $this->paid = true;
    }

    protected function applyOutstandingPaymentCalculated(OutstandingPaymentCalculated $event)
    {
        $this->total = $event->getTotal();
    }
}