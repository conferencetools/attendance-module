<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\Ticket;
use JMS\Serializer\Annotation as Jms;

class TicketQuantity
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $ticketId;
    /**
     * @var int
     * @Jms\Type("integer")
     */
    private $quantity;
    /**
     * @var Ticket
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Ticket")
     */
    private $ticket;
    public function __construct(string $ticketId, Ticket $ticket, int $quantity)
    {
        if ($quantity < 0) {
            throw new \DomainException('Quantity must be a positive value');
        }

        $this->ticketId = $ticketId;
        $this->quantity = $quantity;
        $this->ticket = $ticket;
    }

    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function getTotalPrice(): Price
    {
        return $this->ticket->getPrice()->multiply($this->quantity);
    }
}