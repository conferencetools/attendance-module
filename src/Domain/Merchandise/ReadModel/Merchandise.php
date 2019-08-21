<?php

namespace ConferenceTools\Attendance\Domain\Merchandise\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity() */
class Merchandise
{
    /** @ORM\Id @ORM\Column(type="string") */
    private $id;
    /** @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Descriptor") */
    private $descriptor;
    /** @ORM\Column(type="integer") */
    private $quantity;
    /** @ORM\Column(type="integer") */
    private $remaining = 0;
    /** @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $price;
    /** @ORM\Column(type="boolean") */
    private $onSale = false;
    /** @ORM\Column(type="datetime", nullable=true) */
    private $onSaleFrom;
    /** @ORM\Column(type="datetime", nullable=true) */
    private $withdrawFrom;
    /** @ORM\Column(type="boolean") */
    private $requiresTicket;

    public function __construct(string $id, Descriptor $descriptor, int $quantity, Price $price, bool $requiresTicket)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->remaining = $quantity;
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

    public function withdraw()
    {
        $this->onSale = false;
    }

    public function onSale()
    {
        $this->onSale = true;
    }

    public function isOnSale()
    {
        return $this->onSale;
    }

    public function getRemaining(): int
    {
        return $this->remaining;
    }

    public function decreaseRemainingBy(int $quantity): void
    {
        $this->remaining -= $quantity;
    }

    public function increaseRemainingBy(int $quantity): void
    {
        $this->remaining+= $quantity;
    }

    public function getOnSaleFrom(): ?\DateTime
    {
        return $this->onSaleFrom;
    }

    public function onSaleFrom(\DateTime $when): void
    {
        $this->onSaleFrom = $when;
    }

    public function getWithdrawFrom(): ?\DateTime
    {
        return $this->withdrawFrom;
    }

    public function withdrawFrom(\DateTime $when): void
    {
        $this->withdrawFrom = $when;
    }

    public function getSold(): int
    {
        return $this->quantity - $this->remaining;
    }

    public function requiresTicket(): bool
    {
        return $this->requiresTicket;
    }
}