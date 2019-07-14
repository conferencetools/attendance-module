<?php

namespace ConferenceTools\AttendanceTest\Domain\Purchasing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCheckedOut;

class CheckoutTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $purchaseId = 'purchaseId';
        $paymentDue = Price::fromNetCost(100, 20);
        $fixture = new PurchaseCheckedOut($purchaseId, $paymentDue);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PurchaseCheckedOut $sut */
        $sut = $this->getSerializer()->fromArray($data, PurchaseCheckedOut::class);

        $this->tester->assertEquals($purchaseId, $sut->getPurchaseId());
        $this->tester->assertEquals($paymentDue, $sut->getPaymentDue());
    }
}