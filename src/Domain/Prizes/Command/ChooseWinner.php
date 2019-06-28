<?php

namespace ConferenceTools\Attendance\Domain\Prizes\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class ChooseWinner implements HasActorId
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("array<string>") */
    private $entrants;

    public function __construct(string $id, string ...$entrants)
    {
        $this->id = $id;
        $this->entrants = $entrants;
    }

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getEntrants(): array
    {
        return $this->entrants;
    }
}