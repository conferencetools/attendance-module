<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Event\SaleDateScheduled;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class SaleDateScheduledTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $when = $this->datetimeWithoutMs();
        $id = 'id';

        $fixture = new SaleDateScheduled($id, $when);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var SaleDateScheduled $sut */
        $sut = $this->getSerializer()->fromArray($data, SaleDateScheduled::class);

        $this->tester->assertEquals($id, $sut->getId());
        $this->tester->assertEquals($when, $sut->getWhen());
    }
}
