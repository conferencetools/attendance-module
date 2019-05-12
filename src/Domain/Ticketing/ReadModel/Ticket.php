<?php

namespace ConferenceTools\Attendance\Domain\Ticketing\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class Ticket
{
    /** @ORM\Id @ORM\Column(type="string") */
    private $id;
    /** @ORM\Column(type="string") */
    private $eventId;
    /** @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;
    /** @ORM\Column(type="integer") */
    private $quantity;
    /** @ORM\Column(type="integer") */
    private $remaining = 0;
    /** @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $price;
    /** @ORM\Column(type="boolean") */
    private $onSale = false;

    public function __construct(string $id, string $eventId, Descriptor $descriptor, int $quantity, Price $price)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->remaining = $quantity;
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

    public function getPrice(): Price
    {
        return $this->price;
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

    public function decreaseRemainingBy(int $quantity): void
    {
        $this->remaining -= $quantity;
    }

    public function increaseRemainingBy(int $quantity): void
    {
        $this->remaining+= $quantity;
    }
}