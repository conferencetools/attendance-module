<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\Command\CreateDelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class CreateDelegateListTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $fixture = new CreateDelegateList('ownerId', new OptIn('job-offers', 'Receive job offers from us'));
        $data = $this->getSerializer()->toArray($fixture);

        /** @var CreateDelegateList $sut */
        $sut = $this->getSerializer()->fromArray($data, CreateDelegateList::class);

        $this->tester->assertEquals('ownerId', $sut->getOwner());
        $this->tester->assertEquals([new OptIn('job-offers', 'Receive job offers from us')], $sut->getOptIns());
    }
}
