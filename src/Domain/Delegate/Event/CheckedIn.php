<?php

namespace ConferenceTools\Attendance\Domain\Delegate\Event;

use JMS\Serializer\Annotation as Jms;

class CheckedIn
{
    /**
     * @Jms\Type("string")
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
