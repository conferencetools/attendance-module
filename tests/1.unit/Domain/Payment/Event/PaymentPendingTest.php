<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentPending;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PaymentPendingTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new PaymentPending($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PaymentPending $sut */
        $sut = $this->getSerializer()->fromArray($data, PaymentPending::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
    }
}
