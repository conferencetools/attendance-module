<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
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
     * @var Price
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $price;

    public function __construct(string $ticketId, int $quantity, Price $price)
    {
        if ($quantity < 0) {
            throw new \DomainException('Quantity must be a positive value');
        }

        $this->ticketId = $ticketId;
        $this->quantity = $quantity;
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

    public function getTotalPrice(): Price
    {
        return $this->price->multiply($this->quantity);
    }
}