<?php

namespace ConferenceTools\Attendance\Domain\Sponsor\Command;

use JMS\Serializer\Annotation as Jms;

class CreateSponsor
{
    /** @Jms\Type("string") */
    private $name;
    /** @Jms\Type("string") */
    private $user;

    public function __construct(string $name, string $user)
    {
        $this->name = $name;
        $this->user = $user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUser(): string
    {
        return $this->user;
    }
}
