<?php


namespace ConferenceTools\Attendance\Domain\Discounting;


use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\Price;

class Discount
{
    private $percentage;
    private $perTicket;
    private $perPurchase;
    private $forTicketIds = [];

    public function __construct(?int $percentage, ?Price $perTicket, ?Price $perPurchase, string ...$forTicketIds)
    {
        $this->percentage = $percentage;
        $this->perTicket = $perTicket;
        $this->perPurchase = $perPurchase;
        $this->forTicketIds = $forTicketIds;
    }

    public static function percentage(int $percentage, string ...$forTicketIds)
    {
        $instance = new self($percentage, null, null, ...$forTicketIds);
        return $instance;
    }

    public static function perTicket(Price $perTicket, string ...$forTicketIds)
    {
        $instance = new self(null, $perTicket, null, ...$forTicketIds);
        return $instance;
    }

    public static function perPurchase(Price $perPurchase, string ...$forTicketIds)
    {
        $instance = new self(null, null, $perPurchase, ...$forTicketIds);
        return $instance;
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
                    $totalDiscount->add($this->perTicket->multiply($ticket->getQuantity()));
                }
            }
        }

        if ($this->percentage !== null) {
            foreach ($tickets as $ticket) {
                if ($this->accept($ticket)) {
                    $totalDiscount->add($ticket->getTotalPrice()->multiply($this->percentage/100));
                }
            }
        }

        return $totalDiscount;
    }

    private function accept(TicketQuantity $ticket): bool
    {
        return empty($this->forTicketIds) || \in_array($ticket->getTicketId(), $this->forTicketIds, true);
    }
}