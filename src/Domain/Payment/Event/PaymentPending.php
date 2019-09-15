<?php


namespace ConferenceTools\Attendance\Domain\Payment\Event;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class PaymentPending
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