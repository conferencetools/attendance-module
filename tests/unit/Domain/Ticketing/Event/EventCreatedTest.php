<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Event\EventCreated;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class EventCreatedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $eventId = 'eventId';
        $name = 'name';
        $description = 'description';
        $capacity = 100;
        $startsOn = $this->datetimeWithoutMs();
        $endsOn = $this->datetimeWithoutMs();

        $fixture = new EventCreated($eventId, $name, $description, $capacity, $startsOn, $endsOn);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var EventCreated $sut */
        $sut = $this->getSerializer()->fromArray($data, EventCreated::class);

        $this->tester->assertEquals($eventId, $sut->getId());
        $this->tester->assertEquals($name, $sut->getName());
        $this->tester->assertEquals($description, $sut->getDescription());
        $this->tester->assertEquals($capacity, $sut->getCapacity());
        $this->tester->assertEquals($endsOn, $sut->getEndsOn());
        $this->tester->assertEquals($startsOn, $sut->getStartsOn());
    }
}
