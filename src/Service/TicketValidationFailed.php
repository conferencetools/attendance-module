<?php


namespace ConferenceTools\Attendance\Service;


class TicketValidationFailed implements TicketValidation
{
    private $reason;

    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    public function getReason(): string
    {
        return $this->reason;
    }
}