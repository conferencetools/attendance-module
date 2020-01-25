<?php

namespace ConferenceTools\Attendance\Domain\Sponsor\Command;

use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use JMS\Serializer\Annotation as Jms;

class DeleteQuestion
{
    /** @Jms\Type("string") */
    private $sponsorId;
    /** @Jms\Type("string") */
    private $handle;

    public function __construct(string $sponsorId, string $handle)
    {
        $this->sponsorId = $sponsorId;
        $this->handle = $handle;
    }

    public function getSponsorId(): string
    {
        return $this->sponsorId;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }
}
