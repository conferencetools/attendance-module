<?php

namespace ConferenceTools\Attendance\Domain\DataSharing\Event;

use JMS\Serializer\Annotation as Jms;

class CollectionTerminated
{
    /** @Jms\Type("string") */
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
