<?php


namespace ConferenceTools\Attendance\Domain\Prizes\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class ReplaceWinner implements HasActorId
{
    /** @Jms\Type("string")
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
