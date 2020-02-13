<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateAdded;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class DelegateAddedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $fixture = new DelegateAdded('listId', 'delegateId', new OptInConsent('job-offers', true));
        $data = $this->getSerializer()->toArray($fixture);

        /** @var DelegateAdded $sut */
        $sut = $this->getSerializer()->fromArray($data, DelegateAdded::class);

        $this->tester->assertEquals('listId', $sut->getId());
        $this->tester->assertEquals('delegateId', $sut->getDelegateId());
        $this->tester->assertEquals([new OptInConsent('job-offers', true)], $sut->getOptInConsents());
    }
}
