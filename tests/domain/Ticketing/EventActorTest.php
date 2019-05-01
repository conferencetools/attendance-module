<?php

use ConferenceTools\Attendance\Domain\Ticketing\Command\CreateEvent;
use ConferenceTools\Attendance\Domain\Ticketing\Event\EventCreated;
use ConferenceTools\Attendance\Domain\Ticketing\EventActor;
use Phactor\Test\ActorHelper;

/**
 * @covers ConferenceTools\Attendance\Domain\Ticketing\EventActor
 */
class EventActorTest extends \Codeception\Test\Unit
{
    /** @var ActorHelper */
    private $helper;
    private $actorId = '';

    public function _before()
    {
        $this->helper = new ActorHelper(EventActor::class);
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testCreateEvent()
    {
        $startDate = (new \DateTime())->add(new \DateInterval('P1D'));
        $endDate = (new \DateTime())->add(new \DateInterval('P2D'));
        $this->helper->when(
            new CreateEvent(
                'event',
                'description',
                50,
                $startDate,
                $endDate
            )
        );

        $this->helper->expect(new EventCreated(
            $this->actorId,
            'event',
            'description',
            50,
            $startDate,
            $endDate
        ));
    }
}
