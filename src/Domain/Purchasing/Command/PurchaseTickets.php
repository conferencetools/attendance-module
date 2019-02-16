<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Command;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;

class PurchaseTickets
{
    /**
     * @var TicketQuantity[]
     * @Jms\Type("array<ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity>")
     */
    private $tickets;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $email;

    /**
     * @Jms\Type("int")
     */
    private $delegates = -1;

    public function __construct(string $email, $delegates, TicketQuantity ...$tickets)
    {
        $this->tickets = $tickets;
        $this->email = $email;
        $this->delegates = $delegates;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDelegates(): int
    {
        return $this->delegates;
    }
}