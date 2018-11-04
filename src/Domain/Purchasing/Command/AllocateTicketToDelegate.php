<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Command;

use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class AllocateTicketToDelegate implements HasActorId
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $delegateId;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $purchaseId;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $ticketId;

    public function __construct(string $delegateId, string $purchaseId, string $ticketId)
    {
        $this->delegateId = $delegateId;
        $this->purchaseId = $purchaseId;
        $this->ticketId = $ticketId;
    }

    public function getActorId(): string
    {
        return $this->purchaseId;
    }

    public function getDelegateId(): string
    {
        return $this->delegateId;
    }

    public function getTicketId(): string
    {
        return $this->ticketId;
    }
}