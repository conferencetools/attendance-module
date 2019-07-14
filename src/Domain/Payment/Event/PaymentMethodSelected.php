<?php

namespace ConferenceTools\Attendance\Domain\Payment\Event;

use JMS\Serializer\Annotation as Jms;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;

class PaymentMethodSelected
{
    /** @Jms\Type("string") */
    private $id;
    /**  @Jms\Type("ConferenceTools\Attendance\Domain\Payment\PaymentType") */
    private $paymentType;

    public function __construct(string $id, PaymentType $paymentType)
    {
        $this->id = $id;
        $this->paymentType = $paymentType;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPaymentType(): PaymentType
    {
        return $this->paymentType;
    }
}
