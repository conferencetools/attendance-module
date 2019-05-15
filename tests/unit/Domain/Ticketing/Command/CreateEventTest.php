<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Command\CreateEvent;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class CreateEventTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $descriptor = new Descriptor('name', 'description');
        $capacity = 100;
        $startsOn = $this->datetimeWithoutMs();
        $endsOn = $this->datetimeWithoutMs();

        $fixture = new CreateEvent($descriptor, $capacity, $startsOn, $endsOn);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var CreateEvent $sut */
        $sut = $this->getSerializer()->fromArray($data, CreateEvent::class);

        $this->tester->assertEquals($descriptor, $sut->getDescriptor());
        $this->tester->assertEquals($capacity, $sut->getCapacity());
        $this->tester->assertEquals($endsOn, $sut->getEndsOn());
        $this->tester->assertEquals($startsOn, $sut->getStartsOn());
    }
}
