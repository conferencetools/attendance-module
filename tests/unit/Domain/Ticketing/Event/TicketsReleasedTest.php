<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class TicketsReleasedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $ticketId = 'ticketId';
        $eventId = 'eventId';
        $descriptor = new Descriptor('name', 'description');
        $quantity = 10;
        $price = Price::fromNetCost(100, 20);

        $fixture = new TicketsReleased($ticketId, $eventId, $descriptor, $quantity, $price);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var TicketsReleased $sut */
        $sut = $this->getSerializer()->fromArray($data, TicketsReleased::class);

        $this->tester->assertEquals($ticketId, $sut->getId());
        $this->tester->assertEquals($eventId, $sut->getEventId());
        $this->tester->assertEquals($descriptor, $sut->getDescriptor());
        $this->tester->assertEquals($quantity, $sut->getQuantity());
        $this->tester->assertEquals($price, $sut->getPrice());
    }
}
