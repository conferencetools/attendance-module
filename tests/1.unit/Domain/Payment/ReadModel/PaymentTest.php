<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment\ReadModel;

use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use ConferenceTools\Attendance\Domain\Ticketing\Price;

class PaymentTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testCreate()
    {
        $id = 'paymentId';
        $purchaseId = 'purchaseId';
        $due = Price::fromNetCost(300, 20);
        $sut = new Payment($id, $purchaseId, $due);

        $this->assertEquals($id, $sut->getId());
        $this->assertEquals($purchaseId, $sut->getPurchaseId());
        $this->assertEquals($due, $sut->getAmount());
        $this->assertEquals('raised', $sut->getStatus());
        $this->assertNull($sut->getPaymentMethod());
    }

    public function testIsComplete()
    {
        $id = 'paymentId';
        $purchaseId = 'purchaseId';
        $due = Price::fromNetCost(300, 20);
        $sut = new Payment($id, $purchaseId, $due);

        $this->assertFalse($sut->isComplete());

        $sut->setStatus('confirmed');

        $this->assertTrue($sut->isComplete());
    }

    public function testIsPending()
    {
        $id = 'paymentId';
        $purchaseId = 'purchaseId';
        $due = Price::fromNetCost(300, 20);
        $sut = new Payment($id, $purchaseId, $due);

        $this->assertFalse($sut->isPending());

        $sut->setStatus('pending');

        $this->assertTrue($sut->isPending());
    }

    public function testPaymentMethodProvided()
    {
        $id = 'paymentId';
        $purchaseId = 'purchaseId';
        $due = Price::fromNetCost(300, 20);
        $sut = new Payment($id, $purchaseId, $due);
        $sut->setStatus(\ConferenceTools\Attendance\Domain\Payment\Payment::STATUS_STARTED);

        $paymentMethod = new PaymentType('invoice', 86400, true);
        $sut->paymentMethodProvided($paymentMethod);

        $this->assertEquals($paymentMethod, $sut->getPaymentMethod());
    }
}
