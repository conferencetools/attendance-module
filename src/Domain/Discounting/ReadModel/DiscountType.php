<?php


namespace ConferenceTools\Attendance\Domain\Discounting\ReadModel;

use Doctrine\ORM\Mapping as ORM;

use ConferenceTools\Attendance\Domain\Discounting\Discount;
/**
 * @ORM\Entity()
 */
class DiscountType
{
    /**
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @ORM\Column(type="int")
     */
    private $percentage;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $perTicket;
    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $perPurchase;
    /**
     * @ORM\Column(type="json_array")
     */
    private $forTicketIds;
    /**
     * @ORM\Column(type="bool")
     */
    private $available;

    public function __construct(string $id, string $name, Discount $discount, bool $available)
    {
        $this->id = $id;
        $this->name = $name;
        $this->percentage = $discount->getPercentage();
        $this->perTicket = $discount->getPerTicket();
        $this->perPurchase = $discount->getPerPurchase();
        $this->forTicketIds = $discount->getForTicketIds();
        $this->available = $available;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDiscount(): Discount
    {
        return new Discount($this->percentage, $this->perTicket, $this->perPurchase, $this->forTicketIds);
    }

    public function withdraw()
    {
        $this->available = false;
    }

    public function available()
    {
        $this->available = true;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }
}