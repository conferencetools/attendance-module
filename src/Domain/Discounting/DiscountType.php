<?php

namespace ConferenceTools\Attendance\Domain\Discounting;

use ConferenceTools\Attendance\Domain\Discounting\Command\AddCode;
use ConferenceTools\Attendance\Domain\Discounting\Command\CreateDiscount;
use ConferenceTools\Attendance\Domain\Discounting\Command\CheckDiscountAvailability;
use ConferenceTools\Attendance\Domain\Discounting\Event\CodeAdded;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountAvailable;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountCreated;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountWithdrawn;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use Phactor\Actor\AbstractActor;
use Phactor\Message\DomainMessage;

class DiscountType extends AbstractActor
{
    private $name;
    private $discount;
    /** @var AvailabilityDates */
    private $availabilityDates;
    private $codes;
    private $available;

    protected function handleCreateDiscount(CreateDiscount $command)
    {
        $availabilityDates = $command->getAvailabilityDates();
        $this->fire(new DiscountCreated(
            $this->id(),
            $command->getName(),
            $command->getDiscount(),
            $availabilityDates,
            $availabilityDates->availableNow()
        ));

        if ($availabilityDates->availableNow()) {
            $availableUntil = $availabilityDates->getAvailableUntil();

            if ($availableUntil instanceof \DateTime) {
                $this->schedule(new CheckDiscountAvailability($this->id(), $availabilityDates), $availableUntil);
            }
        } else {
            $availableFrom = $availabilityDates->getAvailableFrom();

            if ($availableFrom instanceof \DateTime) {
                $this->schedule(new CheckDiscountAvailability($this->id(), $availabilityDates), $availableFrom);
            }
        }
    }

    protected function applyDiscountCreated(DiscountCreated $event)
    {
        $this->name = $event->getName();
        $this->discount = $event->getDiscount();
        $this->availabilityDates = $event->getAvailabilityDates();
        $this->available = $event->isAvailableNow();
    }

    public function handleCheckDiscountAvailability(CheckDiscountAvailability $message)
    {
        if (!$this->availabilityDates->equals($message->getAvailabilityDates())) {
            return;
        }

        if ($this->available) {
            $this->fire(new DiscountWithdrawn($this->id()));
        } else {
            $this->fire(new DiscountAvailable($this->id()));
            $availableUntil = $this->availabilityDates->getAvailableUntil();

            if ($availableUntil instanceof \DateTime) {
                $this->schedule(new CheckDiscountAvailability($this->id(), $this->availabilityDates), $availableUntil);
            }
        }
    }

    public function applyDiscountWithdrawn(DiscountWithdrawn $message)
    {
        $this->available = false;
    }

    public function applyDiscountAvailable(DiscountAvailable $message)
    {
        $this->available = true;
    }

    public function handleAddCode(AddCode $command)
    {
        $this->fire(new CodeAdded($this->id(), $command->getCode()));
    }

    protected function applyCodeAdded(CodeAdded $event)
    {
        $this->codes[] = $event->getCode();
    }
}