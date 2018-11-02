<?php


namespace ConferenceTools\Attendance\Domain\Purchasing\Event;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\Price;

class OutstandingPaymentCalculated
{
    /**
     * @Jms\Type("string")
     * @var string
     */
    private $purchaseId;
    /**
     * @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price")
     * @var Price
     */
    private $total;

    public function __construct(string $purchaseId, Price $total)
    {
        $this->purchaseId = $purchaseId;
        $this->total = $total;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getTotal(): Price
    {
        return $this->total;
    }
}