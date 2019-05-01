<?php
declare(strict_types=1);

namespace ConferenceTools\Attendance\Domain\Ticketing\Event;

use JMS\Serializer\Annotation as Jms;

class EventCreated
{
    /** @Jms\Type("string") */
    private $id;
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

    public function __construct(string $id, string $name, string $description, int $capacity, \DateTime $startsOn, \DateTime $endsOn)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->startsOn = $startsOn;
        $this->endsOn = $endsOn;
    }

    public function getId(): string
    {
        return $this->id;
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
