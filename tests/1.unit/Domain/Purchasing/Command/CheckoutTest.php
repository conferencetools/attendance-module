<?php

namespace ConferenceTools\AttendanceTest\Domain\Purchasing\Command;

use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use ConferenceTools\Attendance\Domain\Purchasing\Command\Checkout;

class CheckoutTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $purchaseId = 'purchaseId';
        $fixture = new Checkout($purchaseId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var Checkout $sut */
        $sut = $this->getSerializer()->fromArray($data, Checkout::class);

        $this->tester->assertEquals($purchaseId, $sut->getPurchaseId());
        $this->tester->assertEquals($purchaseId, $sut->getActorId());
    }
}