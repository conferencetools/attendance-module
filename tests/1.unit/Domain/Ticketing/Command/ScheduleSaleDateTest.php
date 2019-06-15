<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleSaleDate;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ScheduleSaleDateTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $when = $this->datetimeWithoutMs();
        $id = 'id';

        $fixture = new ScheduleSaleDate($id, $when);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ScheduleSaleDate $sut */
        $sut = $this->getSerializer()->fromArray($data, ScheduleSaleDate::class);

        $this->tester->assertEquals($id, $sut->getActorId());
        $this->tester->assertEquals($when, $sut->getWhen());
    }
}
