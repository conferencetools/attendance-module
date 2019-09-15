<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\Price;

class PurchaseCheckedOut
{
    /** @Jms\Type("string") */
    private $purchaseId;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $paymentDue;

    public function __construct(string $purchaseId, Price $paymentDue)
    {
        $this->purchaseId = $purchaseId;
        $this->paymentDue = $paymentDue;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getPaymentDue(): Price
    {
        return $this->paymentDue;
    }
}