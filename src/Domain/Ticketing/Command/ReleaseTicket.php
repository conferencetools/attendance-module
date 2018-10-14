<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Ticket;

class ReleaseTicket
{
    /**
     * @var Ticket
     */
    private $ticket;
    /**
     * @var string
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Ticket")
     */
    private $quantity;
    /**
     * @var string
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates")
     */
    private $availableDates;

    public function __construct(Ticket $ticket, int $quantity, AvailabilityDates $availableDates)
    {
        $this->ticket = $ticket;
        $this->quantity = $quantity;
        $this->availableDates = $availableDates;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAvailableDates(): AvailabilityDates
    {
        return $this->availableDates;
    }
}