<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\Event;

use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class TicketsWithdrawnFromSaleTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $id = 'id';

        $fixture = new TicketsWithdrawnFromSale($id);
        $data = $this->getSerializer()->toArray($fixture);

        /** @var TicketsWithdrawnFromSale $sut */
        $sut = $this->getSerializer()->fromArray($data, TicketsWithdrawnFromSale::class);

        $this->tester->assertEquals($id, $sut->getId());
    }
}
