<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use JMS\Serializer\Annotation as Jms;

class DelegateListCreated
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("string") */
    private $owner;
    /** @Jms\Type("array<ConferenceTools\Attendance\Domain\DataSharing\OptIn>") */
    private $optIns;

    public function __construct(string $id, string $owner, OptIn ...$optIns)
    {
        $this->id = $id;
        $this->owner = $owner;
        $this->optIns = $optIns;
    }

    public function getId(): string
    {
        return $this->id;
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
