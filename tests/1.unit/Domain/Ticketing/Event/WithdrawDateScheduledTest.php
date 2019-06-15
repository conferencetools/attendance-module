<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Event\WithdrawDateScheduled;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class WithdrawDateScheduledTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $when = $this->datetimeWithoutMs();
        $id = 'id';

        $fixture = new WithdrawDateScheduled($id, $when);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var WithdrawDateScheduled $sut */
        $sut = $this->getSerializer()->fromArray($data, WithdrawDateScheduled::class);

        $this->tester->assertEquals($id, $sut->getId());
        $this->tester->assertEquals($when, $sut->getWhen());
    }
}
