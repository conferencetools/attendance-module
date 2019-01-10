<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\Event;

class TicketsOnSale
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $id;
    /**
     * @var Event
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Event")
     */
    private $ticket;
    /**
     * @var int
     * @Jms\Type("integer")
     */
    private $quantity;
    /**
     * @var Price
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $price;

    public function __construct(string $id, Event $ticket, int $quantity, Price $price)
    {
        $this->id = $id;
        $this->ticket = $ticket;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicket(): \ConferenceTools\Attendance\Domain\Ticketing\Event
    {
        return $this->ticket;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }
}