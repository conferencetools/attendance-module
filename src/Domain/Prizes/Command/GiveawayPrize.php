<?php

namespace ConferenceTools\Attendance\Domain\Prizes\Command;

use JMS\Serializer\Annotation as Jms;

class GiveawayPrize
{
    /**
     * @Jms\Type("string")
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}