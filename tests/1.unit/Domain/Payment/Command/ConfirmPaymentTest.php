<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Command;

use ConferenceTools\Attendance\Domain\Payment\Command\ConfirmPayment;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class ConfirmPaymentTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new ConfirmPayment($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ConfirmPayment $sut */
        $sut = $this->getSerializer()->fromArray($data, ConfirmPayment::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
        $this->tester->assertEquals($paymentId, $sut->getActorId());
    }
}
