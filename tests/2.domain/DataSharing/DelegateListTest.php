<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing;


use ConferenceTools\Attendance\Domain\DataSharing\Command\AddDelegate;
use ConferenceTools\Attendance\Domain\DataSharing\Command\CreateDelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\Command\MakeListAvailable;
use ConferenceTools\Attendance\Domain\DataSharing\Command\SetLastCollectionTime;
use ConferenceTools\Attendance\Domain\DataSharing\Command\SetListAvailableTime;
use ConferenceTools\Attendance\Domain\DataSharing\Command\TerminateCollection;
use ConferenceTools\Attendance\Domain\DataSharing\DelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateAdded;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateListCreated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateUpdated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\LastCollectionTimeSet;
use ConferenceTools\Attendance\Domain\DataSharing\Event\ListAvailableTimeSet;
use ConferenceTools\Attendance\Domain\DataSharing\MessageSubscriptions;
use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use Phactor\Test\ActorTester;
use Phactor\Test\TesterFactory;

/**
 * @covers \ConferenceTools\Attendance\Domain\DataSharing\DelegateList
 */
class DelegateListTest extends \Codeception\Test\Unit
{
    /** @var ActorTester */
    private $helper;
    private $actorId;

    public function _before()
    {
        $this->helper = (new TesterFactory())->actor(DelegateList::class, new MessageSubscriptions());
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testCreateList()
    {
        $this->helper->when(new CreateDelegateList('ownerId', new OptIn('jobs', 'Receive information on Jobs')));
        $this->helper->expect(new DelegateListCreated($this->actorId, 'ownerId', new OptIn('jobs', 'Receive information on Jobs')));
    }

    public function testAddDelegate()
    {
        $this->helper->given([new DelegateListCreated($this->actorId, 'ownerId', new OptIn('jobs', 'Receive information on Jobs'))]);
        $this->helper->when(new AddDelegate($this->actorId, 'delegateId', new OptInConsent('jobs', true)));
        $this->helper->expect(new DelegateAdded($this->actorId, 'delegateId', new OptInConsent('jobs', true)));
    }

    public function testAddExistingDelegate()
    {
        $this->helper->given([
            new DelegateListCreated($this->actorId,'ownerId', new OptIn('jobs', 'Receive information on Jobs')),
            new DelegateAdded($this->actorId, 'delegateId', new OptInConsent('jobs', true))
        ]);

        $this->helper->when(new AddDelegate($this->actorId, 'delegateId', new OptInConsent('jobs', true)));
        $this->helper->expect(new DelegateUpdated($this->actorId, 'delegateId', new OptInConsent('jobs', true)));
    }

    public function testSetListAvailableTime()
    {
        $this->helper->given([new DelegateListCreated($this->actorId,'ownerId', new OptIn('jobs', 'Receive information on Jobs'))]);
        $listAvailableTime = new \DateTime();
        $this->helper->when(new SetListAvailableTime($this->actorId, $listAvailableTime));
        $this->helper->expect(new ListAvailableTimeSet($this->actorId, $listAvailableTime));
        $this->helper->expect(new MakeListAvailable($this->actorId, $listAvailableTime));
    }

    public function testSetLastCollectionTime()
    {
        $this->helper->given([new DelegateListCreated($this->actorId,'ownerId', new OptIn('jobs', 'Receive information on Jobs'))]);
        $lastCollectionTime = new \DateTime();
        $this->helper->when(new SetLastCollectionTime($this->actorId, $lastCollectionTime));
        $this->helper->expect(new LastCollectionTimeSet($this->actorId, $lastCollectionTime));
        $this->helper->expect(new TerminateCollection($this->actorId, $lastCollectionTime));
    }
}