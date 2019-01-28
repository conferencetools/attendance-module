<?php


namespace ConferenceTools\Attendance\Domain\Discounting\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $percentage;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perTicket_net;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perTicket_tax;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perPurchase_net;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $perPurchase_tax;
    /**
     * @ORM\Column(type="json_array")
     */
    private $forTicketIds;
    /**
     * @ORM\Column(type="boolean")
     */
    private $available;
    /**
     * @ORM\OneToMany(targetEntity="ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountCode", mappedBy="discountType", cascade={"all"})
     */
    private $codes;

    public function __construct(string $id, string $name, Discount $discount, bool $available)
    {
        $this->id = $id;
        $this->name = $name;
        $this->percentage = $discount->getPercentage();
        $this->perTicket_net = $discount->getPerTicket() === null ? null : $discount->getPerTicket()->getNet()->getAmount();
        $this->perTicket_tax = $discount->getPerTicket() === null ? null : $discount->getPerTicket()->getTaxRate()->getPercentage();
        $this->perPurchase_net = $discount->getPerPurchase() === null ? null : $discount->getPerPurchase()->getNet()->getAmount();
        $this->perPurchase_tax = $discount->getPerPurchase() === null ? null : $discount->getPerPurchase()->getTaxRate()->getPercentage();
        $this->forTicketIds = $discount->getForTicketIds();
        $this->available = $available;
        $this->codes = new ArrayCollection();
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
        $perTicket = $this->perTicket_net !== null ? Price::fromNetCost(new Money($this->perTicket_net), new TaxRate($this->perTicket_tax)) : null;
        $perPurchase = $this->perPurchase_net !== null ? Price::fromNetCost(new Money($this->perPurchase_net), new TaxRate($this->perPurchase_tax)) : null;

        return new Discount($this->percentage, $perTicket, $perPurchase, $this->forTicketIds);
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

    public function addCode(string $code)
    {
        $this->codes->add(new DiscountCode($this, $code));
    }
}