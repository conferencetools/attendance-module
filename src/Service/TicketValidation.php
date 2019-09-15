<?php


namespace ConferenceTools\Attendance\Service;


interface TicketValidation
{
    public function getReason(): string;
}