<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentConfirmed;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PaymentConfirmedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new PaymentConfirmed($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PaymentConfirmed $sut */
        $sut = $this->getSerializer()->fromArray($data, PaymentConfirmed::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
    }
}
