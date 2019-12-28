<?php

namespace ConferenceTools\Attendance\Domain\Delegate\Command;

class ResendTicketEmail
{
    private $id;
    private $email;
    private $checkinId;

    public function __construct(string $id, string $email, string $checkinId)
    {
        $this->id = $id;
        $this->email = $email;
        $this->checkinId = $checkinId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCheckinId(): string
    {
        return $this->checkinId;
    }
}