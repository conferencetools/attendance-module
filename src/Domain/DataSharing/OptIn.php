<?php

namespace ConferenceTools\Attendance\Domain\DataSharing;

use JMS\Serializer\Annotation as Jms;

class OptIn
{
    /** @Jms\Type("string") */
    private $handle;
    /** @Jms\Type("string") */
    private $question;

    public function __construct(string $handle, string $question)
    {
        $this->handle = $handle;
        $this->question = $question;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getQuestion(): string
    {
        return $this->question;
    }
}
