<?php

namespace ConferenceTools\Attendance\Domain\Discounting;

use ConferenceTools\Attendance\Domain\Discounting\Command\CreateDiscount;
use ConferenceTools\Attendance\Domain\Discounting\Command\CheckDiscountAvailability;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountCreated;
use Phactor\Actor\AbstractActor;

class DiscountType extends AbstractActor
{
    private $name;
    private $discount;
    private $availabilityDates;
    private $codes;

    protected function handleCreateDiscount(CreateDiscount $command)
    {
        $availabilityDates = $command->getAvailabilityDates();
        $this->fire(new DiscountCreated(
            $this->id(),
            $command->getName(),
            $command->getDiscount(),
            $availabilityDates
        ));

        if ($availabilityDates->availableNow()) {
            $availableUntil = $availabilityDates->getAvailableUntil();

            if ($availableUntil instanceof \DateTime) {
                $this->schedule(new CheckDiscountAvailability($this->id()), $availableUntil);
            }
        } else {
            $availableFrom = $availabilityDates->getAvailableFrom();

            if ($availableFrom instanceof \DateTime) {
                $this->schedule(new CheckDiscountAvailability($this->id()), $availableFrom);
            }
        }
    }

    protected function applyDiscountCreated(DiscountCreated $event)
    {
        $this->name = $event->getName();
        $this->discount = $event->getDiscount();
        $this->availabilityDates = $event->getAvailabilityDates();
    }

    public function handleAddCode()
    {
        //discount code created
        if ($availabilityDates->availableNow()) {
            $this->fire(new TicketsOnSale($this->id(), $command->getTicket(), $command->getQuantity(), $command->getPrice()));
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

    public function handleCheckExpiry()
    {
        //raise DiscountAvailable x codes
        //raise DiscountExpired x codes
    }

}