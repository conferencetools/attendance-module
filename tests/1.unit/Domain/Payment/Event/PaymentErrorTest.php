<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentError;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PaymentErrorTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new PaymentError($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PaymentError $sut */
        $sut = $this->getSerializer()->fromArray($data, PaymentError::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
    }
}
