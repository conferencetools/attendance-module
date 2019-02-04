<?php

namespace ConferenceTools\Attendance\Domain\Discounting\Event;

use Phactor\Message\HasActorId;
use JMS\Serializer\Annotation as Jms;

class DiscountAvailable implements HasActorId
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

    public function getActorId(): string
    {
        return $this->id;
    }
}
