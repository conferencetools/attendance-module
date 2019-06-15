<?php


namespace ConferenceTools\AttendanceTest\Domain\Ticketing;


use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Event\EventCreated;
use ConferenceTools\Attendance\Domain\Ticketing\EventProjector;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use Phactor\Test\ProjectorHelper;

class EventProjectorTest extends \Codeception\Test\Unit
{
    /** @var ProjectorHelper */
    private $helper;

    public function _before()
    {
        $this->helper = new ProjectorHelper(EventProjector::class);
    }

    public function testEventCreated()
    {
        $startDate = (new \DateTime())->add(new \DateInterval('P1D'));
        $endDate = (new \DateTime())->add(new \DateInterval('P2D'));
        $this->helper->when( new EventCreated(
            '0',
            new Descriptor('event', 'description'),
            50,
            $startDate,
            $endDate
        ));

        $this->helper->expect(
            new Event(
                '0',
                new Descriptor('event', 'description'),
                50,
                $startDate,
                $endDate
            )
        );
    }
}