<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use JMS\Serializer\Annotation as Jms;

class CreateDelegateList
{
    /** @Jms\Type("string") */
    private $owner;
    /** @Jms\Type("array<ConferenceTools\Attendance\Domain\DataSharing\OptIn>") */
    private $optIns;

    public function __construct(string $owner, OptIn ...$questions)
    {
        $this->owner = $owner;
        $this->optIns = $questions;
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getOptIns(): array
    {
        return $this->optIns;
    }
}