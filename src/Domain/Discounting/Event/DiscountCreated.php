<?php

namespace ConferenceTools\Attendance\Domain\Discounting\Event;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Discounting\AvailabilityDates;

class DiscountCreated
{
    /**
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @Jms\Type("string")
     */
    private $name;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Discounting\Discount")
     */
    private $discount;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Discounting\AvailabilityDates")
     */
    private $availabilityDates;
    /**
     * @Jms\Type("bool")
     */
    private $availableNow;

    public function __construct(string $id, string $name, Discount $discount, AvailabilityDates $availabilityDates, bool $availableNow)
    {
        $this->id = $id;
        $this->name = $name;
        $this->discount = $discount;
        $this->availabilityDates = $availabilityDates;
        $this->availableNow = $availableNow;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDiscount(): Discount
    {
        return $this->discount;
    }

    public function getAvailabilityDates(): AvailabilityDates
    {
        return $this->availabilityDates;
    }

    public function isAvailableNow(): bool
    {
        return $this->availableNow;
    }
}
