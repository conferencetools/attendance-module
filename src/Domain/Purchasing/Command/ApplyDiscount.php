<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Command;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Discounting\Discount;
use Phactor\Message\HasActorId;

class ApplyDiscount implements HasActorId
{
    /**
     * @Jms\Type("string")
     */
    private $purchaseId;
    /**
     * @Jms\Type("string")
     */
    private $discountId;
    /**
     * @Jms\Type("string")
     */
    private $discountCode;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Discounting\Discount")
     */
    private $discount;

    public function __construct(string $purchaseId, string $discountId, string $discountCode, Discount $discount)
    {
        $this->purchaseId = $purchaseId;
        $this->discountId = $discountId;
        $this->discountCode = $discountCode;
        $this->discount = $discount;
    }

    public function getActorId(): string
    {
        return $this->purchaseId;
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