<?php

namespace ConferenceTools\Attendance\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Payment\Event\{PaymentConfirmed,
    PaymentMethodSelected,
    PaymentRaised,
    PaymentTimedOut,
    PaymentMade};
use ConferenceTools\Attendance\Domain\Payment\Payment;
use ConferenceTools\Attendance\Domain\Purchasing\Command\{ApplyDiscount,
    Checkout,
    AllocateTicketToDelegate,
    CheckPurchaseTimeout,
    PurchaseItems};
use ConferenceTools\Attendance\Domain\Purchasing\Event\{DiscountApplied,
    MerchandiseAddedToPurchase,
    MerchandisePurchaseExpired,
    PurchaseCheckedOut,
    OutstandingPaymentCalculated,
    PurchaseCompleted,
    PurchaseStartedBy,
    TicketAllocatedToDelegate,
    TicketReservationExpired,
    TicketsReserved};
use Phactor\Actor\AbstractActor;
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
    private $timeoutHandlingByPayment = false;
    /**
     *
     */
    private $merchandise;

    protected function handlePurchaseItems(PurchaseItems $command)
    {
        $this->fire(new PurchaseStartedBy($this->id(), $command->getEmail(), $command->getDelegates(), $command->getBasket()));

        foreach ($command->getBasket()->getTickets() as $ticket) {
            /** @var TicketQuantity $ticket */
            $this->fire(new TicketsReserved($this->id(), $ticket->getTicketId(), $ticket->getQuantity()));
        }

        foreach ($command->getBasket()->getMerchandise() as $merchandise) {
            /** @var MerchandiseQuantity $merchandise */
            $this->fire(new MerchandiseAddedToPurchase($this->id(), $merchandise->getMerchandiseId(), $merchandise->getQuantity()));
        }

        $this->fire(new OutstandingPaymentCalculated($this->id(), $command->getBasket()->getTotal()));

        $this->schedule(new CheckPurchaseTimeout($this->id()), (new \DateTime())->add(new \DateInterval('PT1800S')));
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
        $this->tickets = $event->getBasket()->getTickets();
        $this->merchandise = $event->getBasket()->getMerchandise();
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
        if (!$this->paid && !$this->timeoutHandlingByPayment) {
            $this->expireTickets();
        }
    }

    /**
     * @deprecated
     */
    protected function applyPaymentMade(PaymentMade $event)
    {
        $this->paid = true;
    }

    protected function applyOutstandingPaymentCalculated(OutstandingPaymentCalculated $event)
    {
        $this->total = $event->getTotal();
    }

    protected function handleCheckout(Checkout $message)
    {
        //@TODO check that there aren't unallocated tickets, not paid, not checked out already etc
        $this->fire(new PurchaseCheckedOut($this->id(), $this->total));
        //@TODO handle this event and set a flag to prevent double checkouts?
    }

    protected function handlePaymentRaised(PaymentRaised $message)
    {
        $this->subscribe(Payment::class, $message->getId());
    }

    protected function applyPaymentMethodSelected(PaymentMethodSelected $message)
    {
        $this->timeoutHandlingByPayment = true;
    }

    protected function handlePaymentConfirmed(PaymentConfirmed $event)
    {
        $this->fire(new PurchaseCompleted($this->id()));
    }

    protected function applyPurchaseCompleted(PurchaseCompleted $event)
    {
        $this->paid = true;
    }

    protected function handlePaymentTimedOut(PaymentTimedOut $message)
    {
        if (!$this->paid) {
            $this->expireTickets();
        }
    }

    private function expireTickets(): void
    {
        foreach ($this->tickets as $ticket) {
            $this->fire(new TicketReservationExpired($this->id(), $ticket->getTicketId(), $ticket->getQuantity()));
        }

        foreach ($this->merchandise as $merchandise) {
            /** @var MerchandiseQuantity $merchandise*/
            $this->fire(new MerchandisePurchaseExpired($this->id(), $merchandise->getMerchandiseId(), $merchandise->getQuantity()));
        }

        //@TODO deallocate any tickets from delegates (maybe leave them in place for now; at some point there needs
        // to be a full delete of unused delegates and the related purchases (GDPR) leaving the tickets allocated
        // makes it easier to add a revive expired purchase option further down the line
    }
}