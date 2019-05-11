<?php

namespace ConferenceTools\Attendance\Domain\Discounting\Command;

use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Discounting\AvailabilityDates;
use JMS\Serializer\Annotation as Jms;

class CreateDiscount
{
    /**
     * @Jms\Type("string")
     */
    private $name;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates")
     */
    private $availabilityDates;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Discounting\Discount")
     */
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
