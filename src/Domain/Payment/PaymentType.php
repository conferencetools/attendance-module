<?php


namespace ConferenceTools\Attendance\Domain\Payment;
use JMS\Serializer\Annotation as Jms;

class PaymentType
{
    /** @Jms\Type("string")*/
    private $name;
    /** @Jms\Type("int") */
    private $paymentTimeout;
    /** @Jms\Type("boolean") */
    private $manualConfirmation;

    public function __construct(string $name, int $paymentTimeout, bool $manualConfirmation)
    {
        $this->name = $name;
        $this->paymentTimeout = $paymentTimeout;
        $this->manualConfirmation = $manualConfirmation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPaymentTimeout(): int
    {
        return $this->paymentTimeout;
    }

    public function requiresManualConfirmation(): bool
    {
        return $this->manualConfirmation;
    }
}