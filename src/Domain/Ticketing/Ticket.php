<?php


namespace ConferenceTools\Attendance\Domain\Ticketing;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Jms;

/**
 * @ORM\Embeddable()
 */
class Ticket
{
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $code;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $description;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price")
     * @var Price
     */
    private $price;

    public function __construct(string $code, string $name, string $description, Price $price)
    {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }
}