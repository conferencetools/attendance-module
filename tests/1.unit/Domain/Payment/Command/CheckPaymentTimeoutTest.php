<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Command;

use ConferenceTools\Attendance\Domain\Payment\Command\CheckPaymentTimeout;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class CheckPaymentTimeoutTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $fixture = new CheckPaymentTimeout($paymentId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var CheckPaymentTimeout $sut */
        $sut = $this->getSerializer()->fromArray($data, CheckPaymentTimeout::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
        $this->tester->assertEquals($paymentId, $sut->getActorId());
    }
}
