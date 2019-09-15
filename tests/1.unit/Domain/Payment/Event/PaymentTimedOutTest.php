<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentTimedOut;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PaymentTimedOutTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new PaymentTimedOut($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PaymentTimedOut $sut */
        $sut = $this->getSerializer()->fromArray($data, PaymentTimedOut::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
    }
}
