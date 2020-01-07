<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Command;

use ConferenceTools\Attendance\Domain\DataSharing\Command\AddDelegate;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class AddDelegateTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $fixture = new AddDelegate('listId', 'delegateId', new OptInConsent('job-offers', true));
        $data = $this->getSerializer()->toArray($fixture);

        /** @var AddDelegate $sut */
        $sut = $this->getSerializer()->fromArray($data, AddDelegate::class);

        $this->tester->assertEquals('listId', $sut->getActorId());
        $this->tester->assertEquals('delegateId', $sut->getDelegateId());
        $this->tester->assertEquals([new OptInConsent('job-offers', true)], $sut->getOptInConsents());
    }
}
