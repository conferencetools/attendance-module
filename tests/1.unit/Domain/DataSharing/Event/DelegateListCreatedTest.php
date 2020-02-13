<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateListCreated;
use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class DelegateListCreatedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $fixture = new DelegateListCreated('listId', 'ownerId', new OptIn('job-offers', 'Receive job offers from us'));
        $data = $this->getSerializer()->toArray($fixture);

        /** @var DelegateListCreated $sut */
        $sut = $this->getSerializer()->fromArray($data, DelegateListCreated::class);

        $this->tester->assertEquals('listId', $sut->getId());
        $this->tester->assertEquals('ownerId', $sut->getOwner());
        $this->tester->assertEquals([new OptIn('job-offers', 'Receive job offers from us')], $sut->getOptIns());
    }
}
