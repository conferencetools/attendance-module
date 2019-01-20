<?php

namespace ConferenceTools\Attendance\Domain\Discounting\Command;

use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;

class CreateDiscount
{
    private $name;
    private $availabilityDates;
    private $discount;

    public function __construct(string $name, AvailabilityDates $availabilityDates, Discount $discount)
    {
        $this->name = $name;
        $this->availabilityDates = $availabilityDates;
        $this->discount = $discount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAvailabilityDates(): AvailabilityDates
    {
        return $this->availabilityDates;
    }

    public function getDiscount(): Discount
    {
        return $this->discount;
    }
}
