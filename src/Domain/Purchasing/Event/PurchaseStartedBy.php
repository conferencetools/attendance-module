<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use ConferenceTools\Attendance\Domain\Purchasing\Basket;
use JMS\Serializer\Annotation as Jms;

class PurchaseStartedBy
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
    private $email;
    /**
     * @Jms\Type("int")
     */
    private $delegates = -1;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Purchasing\Basket")
     */
    private $basket;

    public function __construct(string $id, string $email, int $delegates, Basket $basket)
    {
        $this->id = $id;
        $this->email = $email;
        $this->delegates = $delegates;
        $this->basket = $basket;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDelegates(): int
    {
        return $this->delegates;
    }

    public function getBasket(): Basket
    {
        return $this->basket;
    }
}