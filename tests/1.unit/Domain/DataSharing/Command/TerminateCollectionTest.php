<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\Command\TerminateCollection;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class TerminateCollectionTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $lastCollectionTime = new \DateTime();
        $fixture = new TerminateCollection('listId', $lastCollectionTime);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var TerminateCollection $sut */
        $sut = $this->getSerializer()->fromArray($data, TerminateCollection::class);

        $this->tester->assertEquals('listId', $sut->getActorId());
        $this->tester->assertEquals($lastCollectionTime, $sut->getLastCollectionTime(), '', 1);
    }
}
