<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Event;

class ReleaseTicket
{
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Event")
     */
    private $ticket;
    /**
     * @Jms\Type("int")
     */
    private $quantity;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates")
     */
    private $availableDates;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $price;
    /**
     * @Jms\Type("bool")
     */
    private $private = false;

    public function __construct(Event $ticket, int $quantity, AvailabilityDates $availableDates, Price $price, bool $private)
    {
        $this->ticket = $ticket;
        $this->quantity = $quantity;
        $this->availableDates = $availableDates;
        $this->price = $price;
        $this->private = $private;
    }

    public function getTicket(): Event
    {
        return $this->ticket;
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

    public function isPrivate(): bool
    {
        return $this->private;
    }
}