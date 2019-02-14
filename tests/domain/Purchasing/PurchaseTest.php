<?php

namespace ConferenceTools\AttendanceTest\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\ApplyDiscount;
use ConferenceTools\Attendance\Domain\Purchasing\Command\CheckPurchaseTimeout;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseTickets;
use ConferenceTools\Attendance\Domain\Purchasing\Event\DiscountApplied;
use ConferenceTools\Attendance\Domain\Purchasing\Event\OutstandingPaymentCalculated;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseStartedBy;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketAllocatedToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\Purchase;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
use Phactor\Test\ActorHelper;

/**
 * @covers \ConferenceTools\Attendance\Domain\Purchasing\Purchase
 */
class PurchaseTest extends \Codeception\Test\Unit
{
    /** @var ActorHelper */
    private $helper;
    private $actorId = '';

    public function _before()
    {
        $this->helper = new ActorHelper(Purchase::class);
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testStartPurchase()
    {
        $this->helper->when($this->purchaseTicketsCommand());

        $this->helper->expect($this->purchaseStartedByEvent());
        $this->helper->expect($this->checkPurchaseTimeoutEvent());
        $this->helper->expect($this->outstandingPaymentCalculatedEvent());
        $this->helper->expect($this->ticketsReservedEvent());
    }

    public function testApplyDiscount()
    {
        $this->helper->given($this->purchaseHasStarted());

        $this->helper->when(new ApplyDiscount(
            $this->actorId,
            'discountId',
            'discountCode',
            Discount::percentage(10)
        ));

        $this->helper->expect(new DiscountApplied($this->actorId, 'discountId', 'discountCode'));
        $this->helper->expect(new OutstandingPaymentCalculated($this->actorId, Price::fromNetCost(new Money(9000), new TaxRate(20))));
    }

    public function testAssignTicketToDelegate()
    {
        $this->helper->given($this->purchaseHasStarted());
        $this->helper->when(new AllocateTicketToDelegate('delegateId', $this->actorId, 'ticketId'));
        $this->helper->expect(new TicketAllocatedToDelegate($this->actorId, 'ticketId', 'delegateId'));
    }

    public function testTimeoutPurchaseWhichHasntBeenPaid()
    {
        $this->helper->given($this->purchaseHasStarted());
        $this->helper->when(new CheckPurchaseTimeout($this->actorId));
        $this->helper->expect(new TicketReservationExpired($this->actorId, 'ticketId', 1));
    }

    public function testTimeoutPurchaseWhichHasBeenPaid()
    {
        $messages = $this->purchaseHasStarted();
        $messages[] = new PaymentMade($this->actorId);
        $this->helper->given($messages);
        $this->helper->when(new CheckPurchaseTimeout($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testAssignTicketToDelegateThatsAlreadyAssigned()
    {
        $messages = $this->purchaseHasStarted();
        $messages[] = new TicketAllocatedToDelegate($this->actorId, 'ticketId', 'delegateId');
        $this->helper->given($messages);
        $this->helper->when(new AllocateTicketToDelegate('delegateId', $this->actorId, 'ticketId'));
        $this->helper->expectNoMoreMessages();
    }

    private function purchaseHasStarted(): array
    {
        return [
            $this->purchaseTicketsCommand(),
            $this->purchaseStartedByEvent(),
            $this->ticketsReservedEvent(),
            $this->outstandingPaymentCalculatedEvent(),
            $this->checkPurchaseTimeoutEvent(),
        ];
    }

    private function purchaseTicketsCommand(): PurchaseTickets
    {
        return new PurchaseTickets(
            'test@email.com',
            1,
            new TicketQuantity(
                'ticketId',
                new Event(
                    'eventcode',
                    'Awesome Event',
                    'Description'
                ),
                1,
                Price::fromNetCost(new Money(10000), new TaxRate(20))
            )
        );
    }

    private function purchaseStartedByEvent(): PurchaseStartedBy
    {
        return new PurchaseStartedBy($this->actorId, 'test@email.com', 1);
    }

    private function checkPurchaseTimeoutEvent(): CheckPurchaseTimeout
    {
        return new CheckPurchaseTimeout($this->actorId);
    }

    private function outstandingPaymentCalculatedEvent(): OutstandingPaymentCalculated
    {
        return new OutstandingPaymentCalculated($this->actorId, Price::fromNetCost(new Money(10000), new TaxRate(20)));
    }

    private function ticketsReservedEvent(): TicketsReserved
    {
        return new TicketsReserved($this->actorId, 'ticketId', 1);
    }
}
