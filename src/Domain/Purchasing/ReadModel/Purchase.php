<?php

namespace ConferenceTools\Attendance\Domain\Purchasing\ReadModel;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Purchase
{
    /**
     * @ORM\Id @ORM\Column(type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="json_array")
     */
    private $tickets = [];
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $isPaid = false;

    /**
     * @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price")
     * @var Price
     */
    private $total;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $discountId;
    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $discountCode;
    /**
     * @ORM\Column(type="integer")
     */
    private $delegates;

    public function __construct(string $id, string $email, int $delegates)
    {
        $this->id = $id;
        $this->email = $email;
        $this->total = Price::fromNetCost(new Money(0), new TaxRate(0));
        $this->delegates = $delegates;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addTickets(string $ticketId, int $quantity): void
    {
        $this->tickets[$ticketId] = $quantity;
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMaxDelegates(): int
    {
        if ($this->delegates > 0) {
            return $this->delegates;
        }

        $delegates = 0;
        foreach ($this->getTickets() as $ticketId => $quantity) {
            $delegates += $quantity;
        }

        return $delegates;
    }

    public function updateTotal(Price $total): void
    {
        $this->total = $total;
    }

    public function getTotal(): Price
    {
        return $this->total;
    }

    public function isPaid(): bool
    {
        return $this->isPaid;
    }

    public function paid(): void
    {
        $this->isPaid = true;
    }

    public function getDiscountId(): ?string
    {
        return $this->discountId;
    }

    public function getDiscountCode(): ?string
    {
        return $this->discountCode;
    }

    public function discountApplied(string $discountId, string $discountCode)
    {
        $this->discountCode = $discountCode;
        $this->discountId = $discountId;
    }
}