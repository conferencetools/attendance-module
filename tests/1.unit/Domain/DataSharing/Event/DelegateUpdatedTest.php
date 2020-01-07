<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing\Event;

use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateUpdated;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use ConferenceTools\Attendance\Test\Unit\MessageTestAbstract;

class DelegateUpdatedTest extends MessageTestAbstract
{
    public function testSerialise()
    {
        $fixture = new DelegateUpdated('listId', 'delegateId', new OptInConsent('job-offers', true));
        $data = $this->getSerializer()->toArray($fixture);

        /** @var DelegateUpdated $sut */
        $sut = $this->getSerializer()->fromArray($data, DelegateUpdated::class);

        $this->tester->assertEquals('listId', $sut->getId());
        $this->tester->assertEquals('delegateId', $sut->getDelegateId());
        $this->tester->assertEquals([new OptInConsent('job-offers', true)], $sut->getOptInConsents());
    }
}
