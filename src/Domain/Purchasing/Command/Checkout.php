<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class Checkout implements HasActorId
{
    /** @Jms\Type("string") */
    private $purchaseId;

    public function __construct(string $purchaseId)
    {
        $this->purchaseId = $purchaseId;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getActorId(): string
    {
        return $this->purchaseId;
    }
}