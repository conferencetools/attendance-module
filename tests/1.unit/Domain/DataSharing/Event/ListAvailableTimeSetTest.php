<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\Event\ListAvailableTimeSet;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class ListAvailableTimeSetTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $listAvailableTime = new \DateTime();
        $fixture = new ListAvailableTimeSet('listId', $listAvailableTime);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ListAvailableTimeSet $sut */
        $sut = $this->getSerializer()->fromArray($data, ListAvailableTimeSet::class);

        $this->tester->assertEquals('listId', $sut->getId());
        $this->tester->assertEquals($listAvailableTime, $sut->getListAvailableTime(), '', 1);
    }
}
