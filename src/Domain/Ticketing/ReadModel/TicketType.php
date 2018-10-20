<?php

namespace ConferenceTools\Attendance\Domain\Ticketing\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Ticket;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class TicketType
{
    /**
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Ticket")
     */
    private $ticket;
    /**
     * @ORM\Column(type="integer")
     */
    private $remaining;

    public function __construct(string $id, Ticket $ticket, int $remaining)
    {
        $this->id = $id;
        $this->ticket = $ticket;
        $this->remaining = $remaining;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
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