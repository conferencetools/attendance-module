<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\Command\MakeListAvailable;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class MakeListAvailableTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $listAvailableTime = new \DateTime();
        $fixture = new MakeListAvailable('listId', $listAvailableTime);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var MakeListAvailable $sut */
        $sut = $this->getSerializer()->fromArray($data, MakeListAvailable::class);

        $this->tester->assertEquals('listId', $sut->getActorId());
        $this->tester->assertEquals($listAvailableTime, $sut->getListAvailableTime(), '', 1);
    }
}
