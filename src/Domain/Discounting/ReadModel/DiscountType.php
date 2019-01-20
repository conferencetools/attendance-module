<?php


namespace ConferenceTools\Attendance\Domain\Discounting\ReadModel;


use ConferenceTools\Attendance\Domain\Discounting\Discount;

class DiscountType
{
    private $id;
    private $name;
    private $percentage;
    private $perTicket;
    private $perPurchase;
    private $forTicketIds;

    public function __construct(string $id, string $name, Discount $discount)
    {
        $this->id = $id;
        $this->name = $name;
        $this->percentage = $discount->getPercentage();
        $this->perTicket = $discount->getPerTicket();
        $this->perPurchase = $discount->getPerPurchase();
        $this->forTicketIds = $discount->getForTicketIds();
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
}