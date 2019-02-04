<?php

namespace ConferenceTools\Attendance\Domain\Discounting\Event;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class DiscountWithdrawn implements HasActorId
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
