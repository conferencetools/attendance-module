<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentStarted;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PaymentStartedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new PaymentStarted($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PaymentStarted $sut */
        $sut = $this->getSerializer()->fromArray($data, PaymentStarted::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
    }
}
