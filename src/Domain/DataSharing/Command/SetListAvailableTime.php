<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class SetListAvailableTime implements HasActorId
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

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getListAvailableTime(): \DateTime
    {
        return $this->listAvailableTime;
    }
}
