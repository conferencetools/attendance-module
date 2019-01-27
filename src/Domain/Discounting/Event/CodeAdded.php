<?php

namespace ConferenceTools\Attendance\Domain\Discounting\Event;

use JMS\Serializer\Annotation as Jms;

class CodeAdded
{
    /**
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @Jms\Type("string")
     */
    private $code;

    public function __construct(string $id, string $code)
    {
        $this->id = $id;
        $this->code = $code;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}