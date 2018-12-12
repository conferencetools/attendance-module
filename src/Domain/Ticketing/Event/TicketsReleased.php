<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Event;

class TicketsReleased
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @var string
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Event")
     */
    private $event;
    /**
     * @var string
     * @Jms\Type("integer")
     */
    private $quantity;
    /**
     * @var string
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates")
     */
    private $availabilityDates;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     * @var Price
     */
    private $price;
    /**
     * @Jms\Type("bool")
     * @var bool
     */
    private $private;

    public function __construct(string $id, Event $event, int $quantity, AvailabilityDates $availabilityDates, Price $price, bool $private)
    {
        $this->id = $id;
        $this->event = $event;
        $this->quantity = $quantity;
        $this->availabilityDates = $availabilityDates;
        $this->price = $price;
        $this->private = $private;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEvent(): Event
    {
        return $this->event;
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

    public function isPrivate(): bool
    {
        return $this->private;
    }
}