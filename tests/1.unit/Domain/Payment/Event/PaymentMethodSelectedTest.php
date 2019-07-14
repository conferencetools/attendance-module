<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\Event;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMethodSelected;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PaymentMethodSelectedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $paymentId = 'payment';
        $paymentType = new PaymentType('name', 300, false);
        $fixture = new PaymentMethodSelected($paymentId, $paymentType);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PaymentMethodSelected $sut */
        $sut = $this->getSerializer()->fromArray($data, PaymentMethodSelected::class);

        $this->tester->assertEquals($paymentId, $sut->getId());
        $this->tester->assertEquals($paymentType, $sut->getPaymentType());
    }
}
