<?php

namespace ConferenceTools\Attendance\Domain\Ticketing\Command;

use Phactor\Message\HasActorId;
use JMS\Serializer\Annotation as Jms;

class WithdrawFromSale implements HasActorId
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
