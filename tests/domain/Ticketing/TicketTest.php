<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Command\PutOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Command\WithdrawFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsWithdrawnFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
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
            new Event('ticket', 'Ticket', 'A Ticket description'),
            10,
            AvailabilityDates::always(),
            Price::fromNetCost(new Money(10000), new TaxRate(20))
        ));
        $this->helper->expect(new TicketsReleased(
            $this->actorId,
            new Event('ticket', 'Ticket', 'A Ticket description'),
            10,
            AvailabilityDates::always(),
            Price::fromNetCost(new Money(10000), new TaxRate(20))
        ));
        $this->helper->expect(new TicketsOnSale(
            $this->actorId,
            new Event('ticket', 'Ticket', 'A Ticket description'),
            10,
            Price::fromNetCost(new Money(10000), new TaxRate(20))
        ));
        $this->helper->expectNoMoreMessages();
    }

    public function testWithdrawTickets()
    {
        $this->helper->given($this->ticketIsOnSale());
        $this->helper->when(new WithdrawFromSale($this->actorId));
        $this->helper->expect(new TicketsWithdrawnFromSale($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testWithdrawTicketsOnSale()
    {
        $messages = $this->ticketIsOnSale();
        $messages[] = new WithdrawFromSale($this->actorId);
        $messages[] = new TicketsWithdrawnFromSale($this->actorId);

        $this->helper->given($messages);
        $this->helper->when(new PutOnSale($this->actorId));
        $this->helper->expect(new TicketsOnSale(
            $this->actorId,
            new Event('ticket', 'Ticket', 'A Ticket description'),
            10,
            Price::fromNetCost(new Money(10000), new TaxRate(20))
        ));
        $this->helper->expectNoMoreMessages();
    }

    public function testWithdrawTicketsOnSaleWithdraw()
    {
        $messages = $this->ticketIsOnSale();
        $messages[] = new WithdrawFromSale($this->actorId);
        $messages[] = new TicketsWithdrawnFromSale($this->actorId);
        $messages[] = new PutOnSale($this->actorId);
        $messages[] = new TicketsOnSale(
            $this->actorId,
            new Event('ticket', 'Ticket', 'A Ticket description'),
            10,
            Price::fromNetCost(new Money(10000), new TaxRate(20))
        );

        $this->helper->given($messages);
        $this->helper->when(new WithdrawFromSale($this->actorId));
        $this->helper->expect(new TicketsWithdrawnFromSale($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    private function ticketIsOnSale()
    {
        return [
            new ReleaseTicket(
                new Event('ticket', 'Ticket', 'A Ticket description'),
                10,
                AvailabilityDates::always(),
                Price::fromNetCost(new Money(10000), new TaxRate(20))
            ),
            new TicketsReleased(
                $this->actorId,
                new Event('ticket', 'Ticket', 'A Ticket description'),
                10,
                AvailabilityDates::always(),
                Price::fromNetCost(new Money(10000), new TaxRate(20))
            ),
            new TicketsOnSale(
                $this->actorId,
                new Event('ticket', 'Ticket', 'A Ticket description'),
                10,
                Price::fromNetCost(new Money(10000), new TaxRate(20))
            )
        ];
    }
}
