<?php
declare(strict_types=1);

namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use JMS\Serializer\Annotation as Jms;

class CreateEvent
{
    /** @Jms\Type("string") */
    private $name;
    /** @Jms\Type("string") */
    private $description;
    /** @Jms\Type("integer") */
    private $capacity;
    /** @Jms\Type("DateTime") */
    private $startsOn;
    /** @Jms\Type("DateTime") */
    private $endsOn;

    public function __construct(string $name, string $description, int $capacity, \DateTime $startsOn, \DateTime $endsOn)
    {
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->startsOn = $startsOn;
        $this->endsOn = $endsOn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
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
