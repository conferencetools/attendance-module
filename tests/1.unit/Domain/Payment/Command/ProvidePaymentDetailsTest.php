<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Command\ProvidePaymentDetails;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class ProvidePaymentDetailsTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new ProvidePaymentDetails($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ProvidePaymentDetails $sut */
        $sut = $this->getSerializer()->fromArray($data, ProvidePaymentDetails::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
        $this->tester->assertEquals($paymentId, $sut->getActorId());
    }
}
