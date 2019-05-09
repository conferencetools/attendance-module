<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;

class ReleaseTicket
{
    /** @Jms\Type("string") */
    private $eventId;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;
    /** @Jms\Type("int") */
    private $quantity;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates") */
    private $availableDates;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $price;

    public function __construct(string $eventId, Descriptor $descriptor, int $quantity, AvailabilityDates $availableDates, Price $price)
    {
        $this->quantity = $quantity;
        $this->availableDates = $availableDates;
        $this->price = $price;
        $this->eventId = $eventId;
        $this->descriptor = $descriptor;
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

    public function getAvailableDates(): AvailabilityDates
    {
        return $this->availableDates;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }
}