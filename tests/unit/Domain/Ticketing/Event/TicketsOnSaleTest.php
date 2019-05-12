<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class TicketsOnSaleTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $id = 'id';

        $fixture = new TicketsOnSale($id);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var TicketsOnSale $sut */
        $sut = $this->getSerializer()->fromArray($data, TicketsOnSale::class);

        $this->tester->assertEquals($id, $sut->getId());
    }
}
