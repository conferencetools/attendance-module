<?php

namespace ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;

class MerchandiseQuantity
{
    /** @Jms\Type("string") */
    private $merchandiseId;
    /** @Jms\Type("integer") */
    private $quantity;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $price;

    public function __construct(string $merchandiseId, int $quantity, Price $price)
    {
        if ($quantity < 0) {
            throw new \DomainException('Quantity must be a positive value');
        }

        $this->merchandiseId = $merchandiseId;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function getMerchandiseId(): string
    {
        return $this->merchandiseId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotalPrice(): Price
    {
        return $this->price->multiply($this->quantity);
    }
}
