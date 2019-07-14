<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment;

use ConferenceTools\Attendance\Domain\Payment\PaymentType;

class PaymentTypeTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testCreate()
    {
        $sut = new PaymentType('name', 300, false);
        $this->tester->assertEquals('name', $sut->getName());
        $this->tester->assertEquals(300, $sut->getPaymentTimeout());
        $this->tester->assertEquals(false, $sut->requiresManualConfirmation());
    }
}
