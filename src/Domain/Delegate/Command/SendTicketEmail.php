<?php


namespace ConferenceTools\Attendance\Domain\Delegate\Command;


class SendTicketEmail
{
    private $delegateId;

    public function __construct(string $delegateId)
    {
        $this->delegateId = $delegateId;
    }

    public function getDelegateId(): string
    {
        return $this->delegateId;
    }
}
