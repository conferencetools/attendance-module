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

    public function __construct(string $email, TicketQuantity ...$tickets)
    {
        $this->tickets = $tickets;
        $this->email = $email;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}