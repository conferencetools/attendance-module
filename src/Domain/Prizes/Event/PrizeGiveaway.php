<?php

namespace ConferenceTools\Attendance\Domain\Prizes\Event;

use JMS\Serializer\Annotation as Jms;

class PrizeGiveaway
{
    /**
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @Jms\Type("string")
     */
    private $name;

    public function __construct(string $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}