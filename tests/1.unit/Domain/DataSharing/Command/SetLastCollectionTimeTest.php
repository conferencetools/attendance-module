<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\Command\SetLastCollectionTime;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class SetLastCollectionTimeTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $lastCollectionTime = new \DateTime();
        $fixture = new SetLastCollectionTime('listId', $lastCollectionTime);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var SetLastCollectionTime $sut */
        $sut = $this->getSerializer()->fromArray($data, SetLastCollectionTime::class);

        $this->tester->assertEquals('listId', $sut->getActorId());
        $this->tester->assertEquals($lastCollectionTime, $sut->getLastCollectionTime(), '', 1);
    }
}
