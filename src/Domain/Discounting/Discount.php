<?php

namespace ConferenceTools\Attendance\Domain\Discounting;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\Price;

class Discount
{
    /**
     * @Jms\Type("integer")
     */
    private $percentage;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $perTicket;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     */
    private $perPurchase;
    /**
     * @Jms\Type("array<string>")
     */
    private $forTicketIds = [];

    public function __construct(?int $percentage, ?Price $perTicket, ?Price $perPurchase, string ...$forTicketIds)
    {
        $this->percentage = $percentage;
        $this->perTicket = $perTicket;
        $this->perPurchase = $perPurchase;
        $this->forTicketIds = $forTicketIds;
    }

    public static function percentage(int $percentage, string ...$forTicketIds): Discount
    {
        return new self($percentage, null, null, ...$forTicketIds);
    }

    public static function perTicket(Price $perTicket, string ...$forTicketIds): Discount
    {
        return new self(null, $perTicket, null, ...$forTicketIds);
    }

    public static function perPurchase(Price $perPurchase, string ...$forTicketIds): Discount
    {
        return new self(null, null, $perPurchase, ...$forTicketIds);
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function getPerTicket(): ?Price
    {
        return $this->perTicket;
    }

    public function getPerPurchase(): ?Price
    {
        return $this->perPurchase;
    }

    public function getForTicketIds(): array
    {
        return $this->forTicketIds;
    }

    public function calculateDiscount(TicketQuantity ...$tickets): Price
    {
        if ($this->perPurchase !== null) {
            return $this->perPurchase;
        }

        $totalDiscount = Price::fromNetCost(0, current($tickets)->getTotalPrice()->getTaxRate());

        if ($this->perTicket !== null) {
            foreach ($tickets as $ticket) {
                if (empty($this->forTicketIds) || \in_array($ticket->getTicketId(), $this->forTicketIds, true)) {
                    $totalDiscount = $totalDiscount->add($this->perTicket->multiply($ticket->getQuantity()));
                }
            }
        }

        if ($this->percentage !== null) {
            foreach ($tickets as $ticket) {
                if ($this->accept($ticket)) {
                    $totalDiscount = $totalDiscount->add($ticket->getTotalPrice()->multiply($this->percentage/100));
                }
            }
        }

        return $totalDiscount;
    }

    private function accept(TicketQuantity $ticket): bool
    {
        return empty($this->forTicketIds) || \in_array($ticket->getTicketId(), $this->forTicketIds, true);
    }

    /** @TODO should be in a view helper probably */
    public function __toString()
    {
        if ($this->perPurchase !== null) {
            return $this->perPurchase->getGross() . ' per purchase.';
        }

        if ($this->perTicket !== null) {
            return $this->perTicket->getGross() . ' per ticket.';
        }

        if ($this->percentage !== null) {
            return $this->percentage . '%';
        }

        return '';
    }
}
