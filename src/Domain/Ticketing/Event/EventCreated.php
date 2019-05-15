<?php
declare(strict_types=1);

namespace ConferenceTools\Attendance\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use JMS\Serializer\Annotation as Jms;
use phpDocumentor\Reflection\DocBlock\Description;

class EventCreated
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("integer") */
    private $capacity;
    /** @Jms\Type("DateTime") */
    private $startsOn;
    /** @Jms\Type("DateTime") */
    private $endsOn;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;

    public function __construct(string $id, Descriptor $descriptor, int $capacity, \DateTime $startsOn, \DateTime $endsOn)
    {
        $this->id = $id;
        $this->capacity = $capacity;
        $this->startsOn = $startsOn;
        $this->endsOn = $endsOn;
        $this->descriptor = $descriptor;
    }

    public function getId(): string
    {
        return $this->id;
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
