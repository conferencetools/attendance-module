<?php
declare(strict_types=1);

namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use JMS\Serializer\Annotation as Jms;

class CreateEvent
{
    /** @Jms\Type("integer") */
    private $capacity;
    /** @Jms\Type("DateTime") */
    private $startsOn;
    /** @Jms\Type("DateTime") */
    private $endsOn;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;

    public function __construct(Descriptor $descriptor, int $capacity, \DateTime $startsOn, \DateTime $endsOn)
    {
        $this->capacity = $capacity;
        $this->startsOn = $startsOn;
        $this->endsOn = $endsOn;
        $this->descriptor = $descriptor;
    }

    public function getDescriptor(): Descriptor
    {
        return $this->descriptor;
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getStartsOn(): \DateTime
    {
        return $this->startsOn;
    }

    public function getEndsOn(): \DateTime
    {
        return $this->endsOn;
    }
}
