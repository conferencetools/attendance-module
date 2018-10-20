<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;
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

    public function __construct(string $ticketId, int $quantity)
    {
        $this->ticketId = $ticketId;
        $this->quantity = $quantity;
    }

    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}