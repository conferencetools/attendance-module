<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Event;

use JMS\Serializer\Annotation as Jms;

class ListAvailableTimeSet
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("DateTime") */
    private $listAvailableTime;

    public function __construct(string $id, \DateTime $listAvailableTime)
    {
        $this->id = $id;
        $this->listAvailableTime = $listAvailableTime;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getListAvailableTime(): \DateTime
    {
        return $this->listAvailableTime;
    }
}
