<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;

class TicketsReleased
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("string") */
    private $eventId;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;
    /** @Jms\Type("integer") */
    private $quantity;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates") */
    private $availabilityDates;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $price;

    public function __construct(string $id, string $eventId, Descriptor $descriptor, int $quantity, AvailabilityDates $availabilityDates, Price $price)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->availabilityDates = $availabilityDates;
        $this->price = $price;
        $this->eventId = $eventId;
        $this->descriptor = $descriptor;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getDescriptor(): Descriptor
    {
        return $this->descriptor;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAvailabilityDates(): AvailabilityDates
    {
        return $this->availabilityDates;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }
}