<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;
use JMS\Serializer\Annotation as Jms;

/** @TODO this will be a summary event containing more information later on; will break BC */
class PurchaseCompleted
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
