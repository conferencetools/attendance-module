<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Command;

use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleWithdrawDate;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ScheduleWithdrawDateTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $when = $this->datetimeWithoutMs();
        $id = 'id';

        $fixture = new ScheduleWithdrawDate($id, $when);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var ScheduleWithdrawDate $sut */
        $sut = $this->getSerializer()->fromArray($data, ScheduleWithdrawDate::class);

        $this->tester->assertEquals($id, $sut->getActorId());
        $this->tester->assertEquals($when, $sut->getWhen());
    }
}
