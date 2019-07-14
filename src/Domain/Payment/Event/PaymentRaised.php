<?php


namespace ConferenceTools\Attendance\Domain\Payment\Event;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Phactor\Message\HasActorId;

class PaymentRaised implements HasActorId
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("string") */
    private $purchaseId;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $paymentDue;

    public function __construct(string $id, string $purchaseId, Price $paymentDue)
    {
        $this->id = $id;
        $this->purchaseId = $purchaseId;
        $this->paymentDue = $paymentDue;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getPaymentDue(): Price
    {
        return $this->paymentDue;
    }

    public function getActorId(): string
    {
        return $this->purchaseId;
    }
}