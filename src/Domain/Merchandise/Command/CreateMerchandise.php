<?php


namespace ConferenceTools\Attendance\Domain\Merchandise\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;

class CreateMerchandise
{
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;
    /** @Jms\Type("int") */
    private $quantity;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $price;
    /** @Jms\Type("boolean") */
    private $requiresTicket;

    public function __construct(Descriptor $descriptor, int $quantity, Price $price, bool $requiresTicket)
    {
        $this->quantity = $quantity;
        $this->price = $price;
        $this->descriptor = $descriptor;
        $this->requiresTicket = $requiresTicket;
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