<?php


namespace ConferenceTools\Attendance\Domain\Payment\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class ConfirmPayment implements HasActorId
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

    public function getActorId(): string
    {
        return $this->id;
    }
}