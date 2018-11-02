<?php


namespace ConferenceTools\Attendance\Domain\Payment\Event;

use JMS\Serializer\Annotation as Jms;
use Carnage\Phactor\Message\HasActorId;

class PaymentMade implements HasActorId
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getActorId(): string
    {
        return $this->id;
    }
}