<?php

namespace ConferenceTools\Attendance\Domain\Ticketing\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TicketsForSale
{
    /**
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Event")
     */
    private $ticket;
    /**
     * @ORM\Column(type="integer")
     */
    private $remaining;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price")
     * @var Price
     */
    private $price;

    public function __construct(string $id, Event $ticket, int $remaining, Price $price)
    {
        $this->id = $id;
        $this->ticket = $ticket;
        $this->remaining = $remaining;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicket(): Event
    {
        return $this->ticket;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }

    public function getPrice(): Price
    {
        return $this->price;
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