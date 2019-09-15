<?php


namespace ConferenceTools\Attendance\Domain\Merchandise\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;

class MerchandiseCreated
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;
    /** @Jms\Type("integer") */
    private $quantity;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $price;
    /** @Jms\Type("boolean") */
    private $requiresTicket;

    public function __construct(string $id, Descriptor $descriptor, int $quantity, Price $price, bool $requiresTicket)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->descriptor = $descriptor;
        $this->requiresTicket = $requiresTicket;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescriptor(): Descriptor
    {
        return $this->descriptor;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function getRequiresTicket(): bool
    {
        return $this->requiresTicket;
    }
}