<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\Event\CollectionTerminated;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class CollectionTerminatedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $lastCollectionTime = new \DateTime();
        $fixture = new CollectionTerminated('listId');
        $data = $this->getSerializer()->toArray($fixture);

        /** @var CollectionTerminated $sut */
        $sut = $this->getSerializer()->fromArray($data, CollectionTerminated::class);

        $this->tester->assertEquals('listId', $sut->getId());
    }
}
