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
}