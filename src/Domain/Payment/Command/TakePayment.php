<?php


namespace ConferenceTools\Attendance\Domain\Payment\Command;


use ConferenceTools\Attendance\Domain\Ticketing\Price;

class TakePayment
{
    private $purchaseId;
    private $amount;
    private $paymentData;
    private $paymentEmail;

    public function __construct(string $purchaseId, Price $amount, $paymentData, string $paymentEmail)
    {
        $this->purchaseId = $purchaseId;
        $this->amount = $amount;
        $this->paymentData = $paymentData;
        $this->paymentEmail = $paymentEmail;
    }

    public function getPurchaseId(): string
    {
        return $this->purchaseId;
    }

    public function getAmount(): Price
    {
        return $this->amount;
    }

    public function getPaymentData()
    {
        return $this->paymentData;
    }

    public function getPaymentEmail(): string
    {
        return $this->paymentEmail;
    }
}