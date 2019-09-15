<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Command;

use ConferenceTools\Attendance\Domain\Purchasing\Basket;
use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;

class PurchaseItems
{
    /**
     * @var TicketQuantity[]
     * @Jms\Type("ConferenceTools\Attendance\Domain\Purchasing\Basket")
     */
    private $basket;
    /**
     * @var string
     * @Jms\Type("string")
     */
    private $email;

    /**
     * @Jms\Type("int")
     */
    private $delegates = -1;

    public function __construct(string $email, $delegates, Basket $basket)
    {
        $this->basket = $basket;
        $this->email = $email;
        $this->delegates = $delegates;
    }

    public function getBasket(): Basket
    {
        return $this->basket;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDelegates(): int
    {
        return $this->delegates;
    }
}