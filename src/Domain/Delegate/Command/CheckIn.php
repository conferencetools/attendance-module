<?php

namespace ConferenceTools\Attendance\Domain\Delegate\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class CheckIn implements HasActorId
{
    /**
     * @Jms\Type("string")
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getActorId(): string
    {
        return $this->id;
    }
}
