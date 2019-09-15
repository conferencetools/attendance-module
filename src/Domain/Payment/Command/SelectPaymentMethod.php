<?php

namespace ConferenceTools\Attendance\Domain\Payment\Command;

use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use JMS\Serializer\Annotation as Jms;
use Phactor\Message\HasActorId;

class SelectPaymentMethod implements HasActorId
{
    /** @Jms\Type("string") */
    private $id;
    /** @Jms\Type("ConferenceTools\Attendance\Domain\Payment\PaymentType") */
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

    public function getActorId(): string
    {
        return $this->id;
    }

    public function getPaymentType(): PaymentType
    {
        return $this->paymentType;
    }
}