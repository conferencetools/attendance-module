<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;

class EventTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testCreate()
    {
        $eventId = 'eventId';
        $name = 'name';
        $description = 'description';
        $descriptor = new Descriptor('name', 'description');
        $capacity = 100;
        $startsOn = new \DateTime();
        $endsOn = new \DateTime();

        $sut = new Event($eventId, $descriptor, $capacity, $startsOn, $endsOn);

        $this->tester->assertEquals($eventId, $sut->getId());
        $this->tester->assertEquals($name, $sut->getName());
        $this->tester->assertEquals($description, $sut->getDescription());
        $this->tester->assertEquals($descriptor, $sut->getDescriptor());
        $this->tester->assertEquals($capacity, $sut->getCapacity());
        $this->tester->assertEquals($endsOn, $sut->getEndsOn());
        $this->tester->assertEquals($startsOn, $sut->getStartsOn());
    }
}
