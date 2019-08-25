<?php

namespace ConferenceTools\Attendance\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Ticketing\Price;
use JMS\Serializer\Annotation as Jms;

class Basket
{
    /**
     * @var TicketQuantity[]
     * @Jms\Type("array<ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity>")
     */
    private $tickets;
    /**
     * @var MerchandiseQuantity[]
     * @Jms\Type("array<ConferenceTools\Attendance\Domain\Purchasing\MerchandiseQuantity>")
     */
    private $merchandise;

    public function __construct(array $tickets, array $merchandise)
    {
        if (empty($tickets) && empty($merchandise)) {
            throw new \DomainException('Cannot create an empty basket');
        }

        $this->tickets = (function (TicketQuantity ...$ticketQuantity): array { return $ticketQuantity; })(...$tickets);
        $this->merchandise = (function (MerchandiseQuantity ...$merchandiseQuantity): array { return $merchandiseQuantity; })(...$merchandise);
    }

    public function getTickets(): array
    {
        return $this->tickets;
    }

    public function getMerchandise(): array
    {
        return $this->merchandise;
    }

    public function getTotal(): Price
    {
        $taxRate = (current($this->tickets) ?: current($this->merchandise))->getTotalPrice()->getTaxRate();
        $price = Price::fromNetCost(0, $taxRate);

        foreach ($this->tickets as $ticketQuantity) {
            $price = $price->add($ticketQuantity->getTotalPrice());
        }

        foreach ($this->merchandise as $merchandiseQuantity) {
            $price = $price->add($merchandiseQuantity->getTotalPrice());
        }

        return $price;
    }
}