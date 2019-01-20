<?php


namespace ConferenceTools\Attendance\Domain\Discounting\Event;


use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;

class DiscountCreated
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var Discount
     */
    private $discount;
    /**
     * @var AvailabilityDates
     */
    private $availabilityDates;

    public function __construct(string $id, string $name, Discount $discount, AvailabilityDates $availabilityDates)
    {
        $this->id = $id;
        $this->name = $name;
        $this->discount = $discount;
        $this->availabilityDates = $availabilityDates;
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
}