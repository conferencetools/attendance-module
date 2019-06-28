<?php

namespace ConferenceTools\Attendance\Domain\Prizes\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class WinnerIs implements HasActorId
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("string") */
    private $winner;
    /** @Jms\Type("array<string>") */
    private $entrants;

    public function __construct(string $id, string $winner, string ...$entrants)
    {
        $this->id = $id;
        $this->winner = $winner;
        $this->entrants = $entrants;
    }

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getWinner(): string
    {
        return $this->winner;
    }

    public function getEntrants(): array
    {
        return $this->entrants;
    }
}