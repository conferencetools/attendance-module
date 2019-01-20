<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Command;


use ConferenceTools\Attendance\Domain\Discounting\Discount;

class ApplyDiscount
{
    private $purchaseId;
    private $discountId;
    private $discountCode;
    private $discount;

    public function __construct(string $purchaseId, string $discountId, string $discountCode, Discount $discount)
    {
        $this->purchaseId = $purchaseId;
        $this->discountId = $discountId;
        $this->discountCode = $discountCode;
        $this->discount = $discount;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getDiscountId(): string
    {
        return $this->discountId;
    }

    public function getDiscountCode(): string
    {
        return $this->discountCode;
    }

    public function getDiscount(): Discount
    {
        return $this->discount;
    }
}