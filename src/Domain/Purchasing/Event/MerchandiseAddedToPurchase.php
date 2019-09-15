<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use JMS\Serializer\Annotation as Jms;

class MerchandiseAddedToPurchase
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("string") */
    private $merchandiseId;
    /** @Jms\Type("integer") */
    private $quantity;

    public function __construct(string $id, string $merchandiseId, int $quantity)
    {
        $this->id = $id;
        $this->merchandiseId = $merchandiseId;
        $this->quantity = $quantity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMerchandiseId(): string
    {
        return $this->merchandiseId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}