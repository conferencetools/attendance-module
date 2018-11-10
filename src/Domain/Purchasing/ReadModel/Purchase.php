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

    public function __construct(string $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
        $this->total = Price::fromNetCost(new Money(0, ''), new TaxRate(0));
    }

    public function addTickets(string $ticketId, int $quantity)
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
        $delegates = 0;
        foreach ($this->getTickets() as $ticketId => $quantity) {
            $delegates += $quantity;
        }

        return $delegates;
    }

    public function updateTotal(Price $total)
    {
        $this->total = $total;
    }

    public function getTotal(): Price
    {
        return $this->total;
    }

    public function isPaid()
    {
        return $this->isPaid;
    }

    public function paid()
    {
        $this->isPaid = true;
    }
}