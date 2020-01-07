<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class AddDelegate implements HasActorId
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("string") */
    private $delegateId;
    /** @Jms\Type("array<ConferenceTools\Attendance\Domain\DataSharing\OptInConsent>") */
    private $optInConsents;

    public function __construct(string $id, string $delegateId, OptInConsent ...$optInConsents)
    {
        $this->id = $id;
        $this->delegateId = $delegateId;
        $this->optInConsents = $optInConsents;
    }

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getDelegateId(): string
    {
        return $this->delegateId;
    }

    public function getOptInConsents(): array
    {
        return $this->optInConsents;
    }
}