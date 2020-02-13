<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\Event\LastCollectionTimeSet;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class LastCollectionTimeSetTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $lastCollectionTime = new \DateTime();
        $fixture = new LastCollectionTimeSet('listId', $lastCollectionTime);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var LastCollectionTimeSet $sut */
        $sut = $this->getSerializer()->fromArray($data, LastCollectionTimeSet::class);

        $this->tester->assertEquals('listId', $sut->getId());
        $this->tester->assertEquals($lastCollectionTime, $sut->getLastCollectionTime(), '', 1);
    }
}
