<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class SetLastCollectionTime implements HasActorId
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

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getLastCollectionTime(): \DateTime
    {
        return $this->lastCollectionTime;
    }
}
