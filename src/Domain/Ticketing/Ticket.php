<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;


use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleSaleDate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleWithdrawDate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBeWithdrawn;
use ConferenceTools\Attendance\Domain\Ticketing\Event\SaleDateScheduled;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBePutOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;
use Phactor\Actor\AbstractActor;
use ConferenceTools\Attendance\Domain\Ticketing\Command\CheckTicketAvailability;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\Event\WithdrawDateScheduled;

class Ticket extends AbstractActor
{
    private $descriptor;
    private $quantity;
    private $onSale = false;
    private $price;
    private $eventId;
    private $putOnSaleOn;
    private $withdrawOn;

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

    protected function handleScheduleWithdrawDate(ScheduleWithdrawDate $command)
    {
        $this->schedule(new ShouldTicketBeWithdrawn($this->id(), $command->getWhen()), $command->getWhen());
        $this->fire(new WithdrawDateScheduled($this->id(), $command->getWhen()));
    }

    protected function applyWithdrawDateScheduled(WithdrawDateScheduled $event)
    {
        $this->withdrawOn = $event->getWhen();
    }

    protected function handleShouldTicketBeWithdrawn(ShouldTicketBeWithdrawn $command)
    {
        if ($this->onSale && $this->withdrawOn == $command->getWhen()) {
            $this->fire(new TicketsWithdrawnFromSale($this->id()));
        }
    }

    protected function applyTicketsWithdrawnFromSale(TicketsWithdrawnFromSale $event)
    {
        $this->onSale = false;
    }
}