<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Command\CreateEvent;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class CreateEventTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $name = 'name';
        $description = 'description';
        $capacity = 100;
        $startsOn = $this->datetimeWithoutMs();
        $endsOn = $this->datetimeWithoutMs();

        $fixture = new CreateEvent($name, $description, $capacity, $startsOn, $endsOn);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var CreateEvent $sut */
        $sut = $this->getSerializer()->fromArray($data, CreateEvent::class);

        $this->tester->assertEquals($name, $sut->getName());
        $this->tester->assertEquals($description, $sut->getDescription());
        $this->tester->assertEquals($capacity, $sut->getCapacity());
        $this->tester->assertEquals($endsOn, $sut->getEndsOn());
        $this->tester->assertEquals($startsOn, $sut->getStartsOn());
    }
}
