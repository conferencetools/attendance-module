<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleSaleDate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleWithdrawDate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBeWithdrawn;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Event\SaleDateScheduled;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBePutOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\WithdrawDateScheduled;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\Ticket;
use Phactor\Test\ActorHelper;

/**
 * @covers \ConferenceTools\Attendance\Domain\Ticketing\Ticket
 */
class TicketTest extends \Codeception\Test\Unit
{
    /** @var ActorHelper */
    private $helper;
    private $actorId = '';

    public function _before()
    {
        $this->helper = new ActorHelper(Ticket::class);
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testReleaseTicket()
    {
        $this->helper->when(new ReleaseTicket(
            'eventId',
            new Descriptor('Ticket', 'A Ticket description'),
            10,
            Price::fromNetCost(10000, 20)
        ));
        $this->helper->expect(new TicketsReleased(
            $this->actorId,
            'eventId',
            new Descriptor('Ticket', 'A Ticket description'),
            10,
            Price::fromNetCost(10000, 20)
        ));
        $this->helper->expectNoMoreMessages();
    }

    public function testWithdrawTickets()
    {
        $withdrawFrom = new \DateTime();
        $this->helper->given($this->ticketIsOnSale());
        $this->helper->when(new ScheduleWithdrawDate($this->actorId, $withdrawFrom));
        $this->helper->expect(new WithdrawDateScheduled($this->actorId, $withdrawFrom));
        $this->helper->expect(new ShouldTicketBeWithdrawn($this->actorId, $withdrawFrom));
        $this->helper->expectNoMoreMessages();
    }

    public function testScheduleTicketOnSale()
    {
        $onSaleFrom = new \DateTime();
        $messages = $this->ticketReleased();

        $this->helper->given($messages);
        $this->helper->when(new ScheduleSaleDate($this->actorId, $onSaleFrom));
        $this->helper->expect(new SaleDateScheduled($this->actorId, $onSaleFrom));
        $this->helper->expect(new ShouldTicketBePutOnSale($this->actorId, $onSaleFrom));
        $this->helper->expectNoMoreMessages();
    }

    public function testScheduleTicketOnSaleAlreadyOnSale()
    {
        $onSaleFrom = new \DateTime();
        $messages = $this->ticketIsOnSale();

        $this->helper->given($messages);
        $this->helper->when(new ScheduleSaleDate($this->actorId, $onSaleFrom));
        $this->helper->expectNoMoreMessages();
    }

    public function testShouldTicketsBeOnSaleSuccess()
    {
        $onSaleFrom = new \DateTime();

        $messages = $this->ticketReleased();
        $messages[] = new SaleDateScheduled($this->actorId, $onSaleFrom);

        $this->helper->given($messages);
        $this->helper->when(new ShouldTicketBePutOnSale($this->actorId, $onSaleFrom));
        $this->helper->expect(new TicketsOnSale($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testShouldTicketsBeOnSaleAlreadyOnSale()
    {
        $onSaleFrom = new \DateTime();

        $messages = $this->ticketReleased();
        $messages[] = new SaleDateScheduled($this->actorId, $onSaleFrom);
        $messages[] = new TicketsOnSale($this->actorId);

        $this->helper->given($messages);
        $this->helper->when(new ShouldTicketBePutOnSale($this->actorId, $onSaleFrom));
        $this->helper->expectNoMoreMessages();
    }

    public function testShouldTicketsBeOnSaleDifferentDate()
    {
        $onSaleFrom = new \DateTime();

        $messages = $this->ticketReleased();
        $messages[] = new SaleDateScheduled($this->actorId, $onSaleFrom);

        $this->helper->given($messages);
        $this->helper->when(new ShouldTicketBePutOnSale($this->actorId, (clone $onSaleFrom)->add(new \DateInterval('P1D'))));
        $this->helper->expectNoMoreMessages();
    }

    public function testShouldTicketsBeWithdrawnSuccess()
    {
        $onSaleFrom = new \DateTime();

        $messages = $this->ticketIsOnSale();
        $messages[] = new WithdrawDateScheduled($this->actorId, $onSaleFrom);

        $this->helper->given($messages);
        $this->helper->when(new ShouldTicketBeWithdrawn($this->actorId, $onSaleFrom));
        $this->helper->expect(new TicketsWithdrawnFromSale($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testShouldTicketsBeWithdrawnAlreadyOnSale()
    {
        $onSaleFrom = new \DateTime();

        $messages = $this->ticketReleased();
        $messages[] = new WithdrawDateScheduled($this->actorId, $onSaleFrom);

        $this->helper->given($messages);
        $this->helper->when(new ShouldTicketBeWithdrawn($this->actorId, $onSaleFrom));
        $this->helper->expectNoMoreMessages();
    }

    public function testShouldTicketsBeWithdrawnDifferentDate()
    {
        $onSaleFrom = new \DateTime();

        $messages = $this->ticketReleased();
        $messages[] = new TicketsOnSale($this->actorId);
        $messages[] = new WithdrawDateScheduled($this->actorId, $onSaleFrom);

        $this->helper->given($messages);
        $this->helper->when(new ShouldTicketBeWithdrawn($this->actorId, (clone $onSaleFrom)->add(new \DateInterval('P1D'))));
        $this->helper->expectNoMoreMessages();
    }

    private function ticketIsOnSale(): array
    {
        $ticketMessages = $this->ticketReleased();
        $ticketMessages[] = new TicketsOnSale($this->actorId);

        return $ticketMessages;
    }

    private function ticketReleased(): array
    {
        return [
            new ReleaseTicket(
                'eventId',
                new Descriptor('Ticket', 'A Ticket description'),
                10,
                Price::fromNetCost(10000, 20)
            ),
            new TicketsReleased(
                $this->actorId,
                'eventId',
                new Descriptor('Ticket', 'A Ticket description'),
                10,
                Price::fromNetCost(10000, 20)
            ),
        ];
    }
}
