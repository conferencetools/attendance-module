<?php

namespace ConferenceTools\Attendance\Domain\Sponsor\Command;

use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use JMS\Serializer\Annotation as Jms;

class AddQuestion
{
    /** @Jms\Type("string") */
    private $sponsorId;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\DataSharing\OptIn") */
    private $question;

    public function __construct(string $sponsorId, OptIn $question)
    {
        $this->sponsorId = $sponsorId;
        $this->question = $question;
    }

    public function getSponsorId(): string
    {
        return $this->sponsorId;
    }

    public function getQuestion(): OptIn
    {
        return $this->question;
    }
}
