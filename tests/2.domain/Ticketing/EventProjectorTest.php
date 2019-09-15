<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing;

use Codeception\Test\Unit;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Event\EventCreated;
use ConferenceTools\Attendance\Domain\Ticketing\EventProjector;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use Phactor\ReadModel\InMemoryRepository;
use Phactor\ReadModel\Repository;
use Phactor\Test\ProjectorHelper;

class EventProjectorTest extends Unit
{
    /** @var ProjectorHelper */
    private $helper;

    public function _before()
    {
        $factory = function (Repository $repository) {
            $ticketRepository = new InMemoryRepository();
            $ticketRepository->add(
                new Ticket('0', '0', new Descriptor('name', 'desc'), 20, Price::fromNetCost(100, 20))
            );
            return new EventProjector($repository, $ticketRepository);
        };
        $this->helper = ProjectorHelper::fromFactory($factory);
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

    public function testTicketReserved()
    {
        $startDate = (new \DateTime())->add(new \DateInterval('P1D'));
        $endDate = (new \DateTime())->add(new \DateInterval('P2D'));
        $event = new Event(
            '0',
            new Descriptor('event', 'description'),
            50,
            $startDate,
            $endDate
        );

        $this->helper->given($event);
        $this->helper->when( new TicketsReserved('purchase', '0',5));

        $expected = new Event(
            '0',
            new Descriptor('event', 'description'),
            50,
            $startDate,
            $endDate
        );
        $expected->increaseRegistered(5);

        $this->helper->expect($expected);
    }

    public function testTicketReservationExpired()
    {
        $startDate = (new \DateTime())->add(new \DateInterval('P1D'));
        $endDate = (new \DateTime())->add(new \DateInterval('P2D'));
        $event = new Event(
            '0',
            new Descriptor('event', 'description'),
            50,
            $startDate,
            $endDate
        );

        $this->helper->given($event);
        $this->helper->when( new TicketReservationExpired('purchase', '0',5));

        $expected = new Event(
            '0',
            new Descriptor('event', 'description'),
            50,
            $startDate,
            $endDate
        );
        $expected->decreaseRegistered(5);

        $this->helper->expect($expected);
    }
}