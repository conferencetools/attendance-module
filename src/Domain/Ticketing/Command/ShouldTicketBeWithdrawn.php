<?php


namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class ShouldTicketBeWithdrawn implements HasActorId
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