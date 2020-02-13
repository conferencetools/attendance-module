<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\Event\ListAvailable;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class ListAvailableTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $listAvailableTime = new \DateTime();
        $fixture = new ListAvailable('listId');
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ListAvailable $sut */
        $sut = $this->getSerializer()->fromArray($data, ListAvailable::class);

        $this->tester->assertEquals('listId', $sut->getId());
    }
}
