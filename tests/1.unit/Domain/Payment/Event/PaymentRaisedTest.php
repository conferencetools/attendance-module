<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentRaised;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PaymentRaisedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $purchaseId = 'purchase';
        $paymentDue = Price::fromNetCost(1000, 20);
        $fixture = new PaymentRaised($paymentId, $purchaseId, $paymentDue);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PaymentRaised $sut */
        $sut = $this->getSerializer()->fromArray($data, PaymentRaised::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
        $this->tester->assertEquals($purchaseId, $sut->getPurchaseId());
        $this->tester->assertEquals($purchaseId, $sut->getActorId());
        $this->tester->assertEquals($paymentDue, $sut->getPaymentDue());
    }
}
