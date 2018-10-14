<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Event;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Ticket;

class TicketsReleased
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @var string
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Ticket")
     */
    private $ticket;
    /**
     * @var string
     * @Jms\Type("integer")
     */
    private $quantity;
    /**
     * @var string
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates")
     */
    private $availabilityDates;

    public function __construct(string $id, Ticket $ticket, int $quantity, AvailabilityDates $availabilityDates)
    {
        $this->id = $id;
        $this->ticket = $ticket;
        $this->quantity = $quantity;
        $this->availabilityDates = $availabilityDates;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAvailabilityDates(): AvailabilityDates
    {
        return $this->availabilityDates;
    }
}