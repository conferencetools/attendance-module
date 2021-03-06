<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\TicketProjector;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Phactor\Test\ProjectorHelper;

/**
 * @covers \ConferenceTools\Attendance\Domain\Ticketing\TicketProjector
 */
class TicketProjectorTest extends \Codeception\Test\Unit
{
    /** @var ProjectorHelper */
    private $helper;

    public function _before()
    {
        $this->helper = ProjectorHelper::fromClassName(TicketProjector::class);
    }

    public function testTicketsReleased()
    {
        $this->helper->when(new Event\TicketsReleased(
            'id',
            'eventId',
            new Descriptor('Ticket', 'A Ticket description'),
            10,
            Price::fromNetCost(10000, 20)
        ));

        $this->helper->expect($this->ticketEntity());
    }

    public function testTicketsOnSale()
    {
        $this->helper->given($this->ticketEntity());

        $this->helper->when(new Event\TicketsOnSale('0'));

        $ticket = $this->ticketEntity();
        $ticket->onSale();
        $this->helper->expect($ticket);
    }

    public function testTicketsWithdrawnFromSale()
    {
        $ticket = $this->ticketEntity();
        $ticket->onSale();
        $this->helper->given($ticket);

        $this->helper->when(new Event\TicketsWithdrawnFromSale(
            '0'
        ));

        $this->helper->expect($this->ticketEntity());
    }


    public function testTicketsReserved()
    {
        $ticket = $this->ticketEntity();
        $ticket->onSale();
        $this->helper->given($ticket);

        $this->helper->when(new TicketsReserved(
            'purchaseId',
            '0',
            2
        ));

        $expected = $this->ticketEntity();
        $expected->decreaseRemainingBy(2);
        $expected->onSale();
        $this->helper->expect($expected);
    }

    public function testTicketsExpired()
    {
        $ticket = $this->ticketEntity();
        $ticket->onSale();
        $this->helper->given($ticket);

        $this->helper->when(new TicketReservationExpired(
            'purchaseId',
            '0',
            2
        ));

        $expected = $this->ticketEntity();
        $expected->increaseRemainingBy(2);
        $expected->onSale();
        $this->helper->expect($expected);
    }

    public function testTicketsWithdrawnDateScheduled()
    {
        $ticket = $this->ticketEntity();
        $this->helper->given($ticket);

        $from = new \DateTime();

        $this->helper->when(new Event\WithdrawDateScheduled(
            '0',
            $from
        ));

        $expected = $this->ticketEntity();
        $expected->withdrawFrom($from);
        $this->helper->expect($expected);
    }

    public function testTicketsSaleDateScheduled()
    {
        $ticket = $this->ticketEntity();
        $this->helper->given($ticket);

        $from = new \DateTime();

        $this->helper->when(new Event\SaleDateScheduled(
            '0',
            $from
        ));

        $expected = $this->ticketEntity();
        $expected->onSaleFrom($from);
        $this->helper->expect($expected);
    }

    private function ticketEntity(): Ticket
    {
        $ticket = new Ticket(
            'id',
            'eventId',
            new Descriptor('Ticket', 'A Ticket description'),
            10,
            Price::fromNetCost(10000, 20)
        );
        return $ticket;
    }
}
