<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Event;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\Ticket;

class TicketsOnSale
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var Ticket
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Ticket")
     */
    private $ticket;
    /**
     * @var int
     * @Jms\Type("integer")
     */
    private $quantity;

    public function __construct(string $id, Ticket $ticket, int $quantity)
    {
        $this->id = $id;
        $this->ticket = $ticket;
        $this->quantity = $quantity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicket(): \ConferenceTools\Attendance\Domain\Ticketing\Ticket
    {
        return $this->ticket;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}