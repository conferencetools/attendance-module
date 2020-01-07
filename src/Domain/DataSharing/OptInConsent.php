<?php

namespace ConferenceTools\Attendance\Domain\DataSharing;

use JMS\Serializer\Annotation as Jms;

class OptInConsent
{
    /** @Jms\Type("string") */
    private $handle;
    /** @Jms\Type("boolean") */
    private $consentGranted;

    public function __construct(string $handle, bool $consentGranted)
    {
        $this->handle = $handle;
        $this->consentGranted = $consentGranted;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function isConsentGranted(): bool
    {
        return $this->consentGranted;
    }
}
