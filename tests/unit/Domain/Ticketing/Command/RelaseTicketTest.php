<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class ReleaseTicketTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $eventId = 'eventId';
        $descriptor = new Descriptor('name', 'description');
        $quantity = 10;
        $price = Price::fromNetCost(new Money(100), 20);

        $fixture = new ReleaseTicket($eventId, $descriptor, $quantity, $price);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ReleaseTicket $sut */
        $sut = $this->getSerializer()->fromArray($data, ReleaseTicket::class);

        $this->tester->assertEquals($eventId, $sut->getEventId());
        $this->tester->assertEquals($descriptor, $sut->getDescriptor());
        $this->tester->assertEquals($quantity, $sut->getQuantity());
        $this->tester->assertEquals($price, $sut->getPrice());
    }
}
