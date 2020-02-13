<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Event;

use JMS\Serializer\Annotation as Jms;

class LastCollectionTimeSet
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("DateTime") */
    private $lastCollectionTime;

    public function __construct(string $id, \DateTime $lastCollectionTime)
    {
        $this->id = $id;
        $this->lastCollectionTime = $lastCollectionTime;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLastCollectionTime(): \DateTime
    {
        return $this->lastCollectionTime;
    }
}
