<?php

namespace ConferenceTools\Attendance\PaymentProvider;

use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use Zend\View\Model\ViewModel;

interface PaymentProvider
{
    public function getView(Purchase $purchase, Payment $payment): ViewModel;
}