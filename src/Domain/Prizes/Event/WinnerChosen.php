<?php


namespace ConferenceTools\Attendance\Domain\Prizes\Event;

use JMS\Serializer\Annotation as Jms;

class WinnerChosen
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("string") */
    private $winner;

    public function __construct(string $id, string $winner)
    {
        $this->id = $id;
        $this->winner = $winner;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getWinner(): string
    {
        return $this->winner;
    }
}