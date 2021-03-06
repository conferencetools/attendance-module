<?php

namespace ConferenceTools\Attendance\Domain\Purchasing\Command;
use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class CheckPurchaseTimeout implements HasActorId
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
