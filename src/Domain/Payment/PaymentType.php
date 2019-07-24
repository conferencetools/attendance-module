<?php


namespace ConferenceTools\Attendance\Domain\Payment;
use JMS\Serializer\Annotation as Jms;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class PaymentType
{
    /**
     * @Jms\Type("string")
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @Jms\Type("int")
     * @ORM\Column(type="integer")
     */
    private $paymentTimeout;
    /**
     * @ORM\Column(type="boolean")
     * @Jms\Type("boolean")
     */
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