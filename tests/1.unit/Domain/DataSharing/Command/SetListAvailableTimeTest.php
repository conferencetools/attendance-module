<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\Command\SetListAvailableTime;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class SetListAvailableTimeTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $listAvailableTime = new \DateTime();
        $fixture = new SetListAvailableTime('listId', $listAvailableTime);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var SetListAvailableTime $sut */
        $sut = $this->getSerializer()->fromArray($data, SetListAvailableTime::class);

        $this->tester->assertEquals('listId', $sut->getActorId());
        $this->tester->assertEquals($listAvailableTime, $sut->getListAvailableTime(), '', 1);
    }
}
