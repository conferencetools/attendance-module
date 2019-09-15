<?php


namespace ConferenceTools\Attendance\Domain\Merchandise\Event;

use JMS\Serializer\Annotation as Jms;

class WithdrawDateScheduled
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("DateTime")*/
    private $when;

    public function __construct(string $id, \DateTime $when)
    {
        $this->id = $id;
        $this->when = $when;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getWhen(): \DateTime
    {
        return $this->when;
    }
}
