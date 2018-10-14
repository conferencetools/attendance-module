<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use JMS\Serializer\Annotation as Jms;

class TicketAllocatedToDelegate
{
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $id;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $ticketId;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $delegateId;

    public function __construct(string $id, string $ticketId, string $delegateId)
    {
        $this->id = $id;
        $this->ticketId = $ticketId;
        $this->delegateId = $delegateId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTicketId(): string
    {
        return $this->ticketId;
    }

    public function getDelegateId(): string
    {
        return $this->delegateId;
    }
}