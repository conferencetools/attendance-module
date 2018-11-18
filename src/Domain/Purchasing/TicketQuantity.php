<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
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
     * @var Event
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Event")
     */
    private $ticket;
    /**
     * @var Price
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $price;

    public function __construct(string $ticketId, Event $event, int $quantity, Price $price)
    {
        if ($quantity < 0) {
            throw new \DomainException('Quantity must be a positive value');
        }

        $this->ticketId = $ticketId;
        $this->quantity = $quantity;
        $this->ticket = $event;
        $this->price = $price;
    }

    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTicket(): Event
    {
        return $this->ticket;
    }

    public function getTotalPrice(): Price
    {
        return $this->price->multiply($this->quantity);
    }
}