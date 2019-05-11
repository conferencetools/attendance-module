<?php

namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use Phactor\Message\HasActorId;
use JMS\Serializer\Annotation as Jms;

class ScheduleSaleDate implements HasActorId
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("DateTime") */
    private $when;

    public function __construct(string $id, \DateTime $when)
    {
        $this->id = $id;
        $this->when = $when;
    }

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getWhen(): \DateTime
    {
        return $this->when;
    }
}
