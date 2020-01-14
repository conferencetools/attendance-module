<?php

namespace ConferenceTools\AttendanceTest\Domain\DataSharing;


use ConferenceTools\Attendance\Domain\DataSharing\Command\AddDelegate;
use ConferenceTools\Attendance\Domain\DataSharing\Command\CreateDelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\Command\MakeListAvailable;
use ConferenceTools\Attendance\Domain\DataSharing\Command\SendDelegateNotifications as SendDelegateNotificationsCommand;
use ConferenceTools\Attendance\Domain\DataSharing\Command\SetLastCollectionTime;
use ConferenceTools\Attendance\Domain\DataSharing\Command\SetListAvailableTime;
use ConferenceTools\Attendance\Domain\DataSharing\Command\TerminateCollection;
use ConferenceTools\Attendance\Domain\DataSharing\DelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\Event\CollectionTerminated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateAdded;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateListCreated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateUpdated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\LastCollectionTimeSet;
use ConferenceTools\Attendance\Domain\DataSharing\Event\ListAvailableTimeSet;
use ConferenceTools\Attendance\Domain\DataSharing\MessageSubscriptions;
use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use ConferenceTools\Attendance\Domain\DataSharing\SendDelegateNotifications;
use Phactor\Test\ActorTester;
use Phactor\Test\TesterFactory;

/**
 * @covers \ConferenceTools\Attendance\Domain\DataSharing\SendDelegateNotifications
 */
class SendDelegateNotificationsTest extends \Codeception\Test\Unit
{
    /** @var ActorTester */
    private $helper;
    private $actorId;

    public function _before()
    {
        $this->helper = (new TesterFactory())->actor(SendDelegateNotifications::class, new MessageSubscriptions());
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testCollectionTerminated()
    {
        $this->helper->when(new DelegateListCreated($this->actorId, 'ownerId', new OptIn('jobs', 'Receive information on Jobs')));
        $this->helper->when(new CollectionTerminated($this->actorId));
        $this->helper->expect(new SendDelegateNotificationsCommand());
    }
}