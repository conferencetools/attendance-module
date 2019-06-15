<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBeWithdrawn;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class ShouldTicketBeOnWithdrawnTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $when = $this->datetimeWithoutMs();
        $id = 'id';

        $fixture = new ShouldTicketBeWithdrawn($id, $when);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ShouldTicketBeWithdrawn $sut */
        $sut = $this->getSerializer()->fromArray($data, ShouldTicketBeWithdrawn::class);

        $this->tester->assertEquals($id, $sut->getActorId());
        $this->tester->assertEquals($when, $sut->getWhen());
    }
}
