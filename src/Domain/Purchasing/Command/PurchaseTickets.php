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

    public function __construct(TicketQuantity ...$tickets)
    {
        $this->tickets = $tickets;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }
}