<?php

namespace ConferenceTools\Attendance\Domain\Ticketing\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Ticket
{
    /**
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Event")
     */
    private $event;
    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;
    /**
     * @ORM\Column(type="integer")
     */
    private $remaining = 0;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price")
     * @var Price
     */
    private $price;
    /**
     * @var AvailabilityDates
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates")
     */
    private $availabilityDates;
    /**
     * @ORM\Column(type="boolean")
     */
    private $onSale = false;

    public function __construct(string $id, Event $event, int $quantity, Price $price, AvailabilityDates $availabilityDates)
    {
        $this->id = $id;
        $this->event = $event;
        $this->quantity = $quantity;
        $this->remaining = $quantity;
        $this->price = $price;
        $this->availabilityDates = $availabilityDates;
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

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getAvailabilityDates(): AvailabilityDates
    {
        return $this->availabilityDates;
    }

    public function withdraw()
    {
        $this->onSale = false;
    }

    public function onSale()
    {
        $this->onSale = true;
    }

    public function isOnSale()
    {
        return $this->onSale;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }

    public function remaining(): int
    {
        return $this->remaining;
    }

    public function decreaseRemainingBy(int $quantity): void
    {
        $this->remaining -= $quantity;
    }

    public function increaseRemainingBy(int $quantity): void
    {
        $this->remaining+= $quantity;
    }
}