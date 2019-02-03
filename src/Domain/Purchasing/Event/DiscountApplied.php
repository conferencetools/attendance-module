<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use JMS\Serializer\Annotation as Jms;

class DiscountApplied
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

    public function __construct(string $purchaseId, string $discountId, string $discountCode)
    {
        $this->purchaseId = $purchaseId;
        $this->discountId = $discountId;
        $this->discountCode = $discountCode;
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
}