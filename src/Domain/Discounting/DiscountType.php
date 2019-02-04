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

    protected function handleCreateDiscount(CreateDiscount $command): void
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

    protected function applyDiscountCreated(DiscountCreated $event): void
    {
        $this->name = $event->getName();
        $this->discount = $event->getDiscount();
        $this->availabilityDates = $event->getAvailabilityDates();
        $this->available = $event->isAvailableNow();
    }

    protected function handleCheckDiscountAvailability(CheckDiscountAvailability $message): void
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

    protected function applyDiscountWithdrawn(DiscountWithdrawn $message): void
    {
        $this->available = false;
    }

    protected function applyDiscountAvailable(DiscountAvailable $message): void
    {
        $this->available = true;
    }

    protected function handleAddCode(AddCode $command): void
    {
        $this->fire(new CodeAdded($this->id(), $command->getCode()));
    }

    protected function applyCodeAdded(CodeAdded $event): void
    {
        $this->codes[] = $event->getCode();
    }
}
