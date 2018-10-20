<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;


use Carnage\Phactor\Actor\AbstractActor;
use ConferenceTools\Attendance\Domain\Ticketing\Command\CheckTicketAvailability;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;

class TicketType extends AbstractActor
{
    /**
     * @var AvailabilityDates
     */
    private $availabilityDates;
    private $ticket;
    private $quantity;
    private $onSale = false;

    protected function handleReleaseTicket(ReleaseTicket $command)
    {
        $availabilityDates = $command->getAvailableDates();

        $this->fire(new TicketsReleased($this->id(), $command->getTicket(), $command->getQuantity(), $availabilityDates));

        if ($availabilityDates->availableNow()) {
            $this->fire(new TicketsOnSale($this->id(), $command->getTicket(), $command->getQuantity()));
            $availableUntil = $availabilityDates->getAvailableUntil();

            if ($availableUntil instanceof \DateTime) {
                $this->schedule(new CheckTicketAvailability($this->id()), $availableUntil);
            }
        } else {
            $availableFrom = $availabilityDates->getAvailableFrom();

            if ($availableFrom instanceof \DateTime) {
                $this->schedule(new CheckTicketAvailability($this->id()), $availableFrom);
            }
        }
    }

    protected function applyTicketsReleased(TicketsReleased $event)
    {
        $this->availabilityDates = $event->getAvailabilityDates();
        $this->ticket = $event->getTicket();
        $this->quantity = $event->getQuantity();
    }

    protected function applyTicketsOnSale(TicketsOnSale $event)
    {
        $this->onSale = true;
    }

    protected function handleCheckTicketAvailability(CheckTicketAvailability $command)
    {
        if (!$this->onSale && $this->availabilityDates->availableNow()) {
            $this->fire(new TicketsOnSale($this->id(), $this->ticket, $this->quantity));
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