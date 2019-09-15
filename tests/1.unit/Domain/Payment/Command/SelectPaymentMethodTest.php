<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Command;

use ConferenceTools\Attendance\Domain\Payment\Command\SelectPaymentMethod;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class SelectPaymentMethodTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $paymentType = new PaymentType('name', 300, false);
        $fixture = new SelectPaymentMethod($paymentId, $paymentType);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var SelectPaymentMethod $sut */
        $sut = $this->getSerializer()->fromArray($data, SelectPaymentMethod::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
        $this->tester->assertEquals($paymentId, $sut->getActorId());
        $this->tester->assertEquals($paymentType, $sut->getPaymentType());
    }
}
