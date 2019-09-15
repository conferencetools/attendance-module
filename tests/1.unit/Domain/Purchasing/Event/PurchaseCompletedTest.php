<?php

namespace ConferenceTools\AttendanceTest\Domain\Purchasing\Event;

use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCompleted;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class PurchaseCompletedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $purchaseId = 'purchaseId';
        $fixture = new PurchaseCompleted($purchaseId);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var PurchaseCompleted $sut */
        $sut = $this->getSerializer()->fromArray($data, PurchaseCompleted::class);

        $this->tester->assertEquals($purchaseId, $sut->getId());
    }
}