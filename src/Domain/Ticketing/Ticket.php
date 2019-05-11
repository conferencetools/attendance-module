<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;


use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleSaleDate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\WithdrawFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\SaleDateScheduled;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBePutOnSale;
use Phactor\Actor\AbstractActor;
use ConferenceTools\Attendance\Domain\Ticketing\Command\CheckTicketAvailability;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;

class Ticket extends AbstractActor
{
    private $descriptor;
    private $quantity;
    private $onSale = false;
    private $price;
    private $eventId;
    private $putOnSaleOn;

    protected function handleReleaseTicket(ReleaseTicket $command)
    {
        $this->fire(new TicketsReleased(
            $this->id(),
            $command->getEventId(),
            $command->getDescriptor(),
            $command->getQuantity(),
            $command->getPrice()
        ));
    }

    protected function applyTicketsReleased(TicketsReleased $event)
    {
        $this->eventId = $event->getEventId();
        $this->descriptor = $event->getDescriptor();
        $this->quantity = $event->getQuantity();
        $this->price = $event->getPrice();
    }

    protected function handleWithdrawFromSale(WithdrawFromSale $command)
    {
        if ($this->onSale) {
            $this->fire(new TicketsWithdrawnFromSale($this->id()));
        }
    }

    protected function handleScheduleSaleDate(ScheduleSaleDate $command)
    {
        if (!$this->onSale) {
            $this->schedule(new ShouldTicketBePutOnSale($this->id(), $command->getWhen()), $command->getWhen());
            $this->fire(new SaleDateScheduled($this->id(), $command->getWhen()));
        }
    }

    protected function applySaleDateScheduled(SaleDateScheduled $event)
    {
        $this->putOnSaleOn = $event->getWhen();
    }

    protected function handleShouldTicketBePutOnSale(ShouldTicketBePutOnSale $command)
    {
        if (!$this->onSale && $this->putOnSaleOn == $command->getWhen()) {
            $this->fire(new TicketsOnSale($this->id()));
        }
    }

    protected function applyTicketsOnSale(TicketsOnSale $event)
    {
        $this->onSale = true;
    }

    protected function handleCheckTicketAvailability(CheckTicketAvailability $command)
    {
        if (!$this->onSale && $this->availabilityDates->availableNow()) {
            $this->fire(new TicketsOnSale($this->id()));
            $availableUntil = $this->availabilityDates->getAvailableUntil();

            if ($availableUntil instanceof \DateTime) {
                $this->schedule(new CheckTicketAvailability($this->id()), $availableUntil);
            }
        }

        if ($this->onSale && !$this->availabilityDates->availableNow()) {
            $this->fire(new TicketsWithdrawnFromSale($this->id()));
        }
    }

    protected function applyTicketsWithdrawnFromSale(TicketsWithdrawnFromSale $event)
    {
        $this->onSale = false;
    }
}