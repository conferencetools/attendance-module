<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBePutOnSale;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class ShouldTicketBeOnSaleTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $when = $this->datetimeWithoutMs();
        $id = 'id';

        $fixture = new ShouldTicketBePutOnSale($id, $when);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ShouldTicketBePutOnSale $sut */
        $sut = $this->getSerializer()->fromArray($data, ShouldTicketBePutOnSale::class);

        $this->tester->assertEquals($id, $sut->getActorId());
        $this->tester->assertEquals($when, $sut->getWhen());
    }
}
