<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use JMS\Serializer\Annotation as Jms;

class TicketsReserved
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $ticketId;
    /**
     * @var integer
     * @Jms\Type("integer")
     */
    private $quantity;

    public function __construct(string $id, string $ticketId, int $quantity)
    {
        $this->id = $id;
        $this->ticketId = $ticketId;
        $this->quantity = $quantity;
    }

    public function getId(): string
    {
        return $this->id;
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