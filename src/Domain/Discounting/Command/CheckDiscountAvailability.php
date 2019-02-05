<?php

namespace ConferenceTools\Attendance\Domain\Discounting\Command;

use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class CheckDiscountAvailability implements HasActorId
{
    /**
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates")
     */
    private $availabilityDates;

    public function __construct(string $id, AvailabilityDates $availabilityDates)
    {
        $this->id = $id;
        $this->availabilityDates = $availabilityDates;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getAvailabilityDates(): AvailabilityDates
    {
        return $this->availabilityDates;
    }
}
