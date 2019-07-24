<?php


namespace ConferenceTools\Attendance\Domain\Payment\ReadModel;

use ConferenceTools\Attendance\Domain\Payment\Payment as PaymentModel;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Payment
{
    /** @ORM\Id() @ORM\Column(type="string") */
    private $id;
    /** @ORM\Column(type="string") */
    private $purchaseId;
    /** @ORM\Embedded("ConferenceTools\Attendance\Domain\Ticketing\Price") */
    private $amount;
    /** @ORM\Column(type="string") */
    private $status;
    /** @ORM\Embedded("ConferenceTools\Attendance\Domain\Payment\PaymentType")  */
    private $paymentMethod;

    public function __construct(string $id, string $purchaseId, Price $amount)
    {
        $this->id = $id;
        $this->purchaseId = $purchaseId;
        $this->amount = $amount;
        $this->status = PaymentModel::STATUS_RAISED;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getAmount(): Price
    {
        return $this->amount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function paymentMethodProvided(PaymentType $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function isComplete(): bool
    {
        return $this->status === PaymentModel::STATUS_CONFIRMED;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentModel::STATUS_PENDING;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getPaymentMethod(): ?PaymentType
    {
        return $this->paymentMethod;
    }
}