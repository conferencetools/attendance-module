<?php

namespace ConferenceTools\AttendanceTest\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentConfirmed;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMethodSelected;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentRaised;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentTimedOut;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Purchasing\Basket;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\ApplyDiscount;
use ConferenceTools\Attendance\Domain\Purchasing\Command\Checkout;
use ConferenceTools\Attendance\Domain\Purchasing\Command\CheckPurchaseTimeout;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseItems;
use ConferenceTools\Attendance\Domain\Purchasing\Event\DiscountApplied;
use ConferenceTools\Attendance\Domain\Purchasing\Event\OutstandingPaymentCalculated;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCheckedOut;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCompleted;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseStartedBy;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketAllocatedToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\Purchase;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
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
        $this->helper->expect(new OutstandingPaymentCalculated($this->actorId, Price::fromNetCost(9000, 20)));
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

    public function testTimeoutPurchaseForWhichAPaymentHasBeenRaised()
    {
        $messages = $this->purchaseHasStarted();
        $messages[] = new PaymentRaised('paymentId', $this->actorId, Price::fromNetCost(300, 20));
        $messages[] = new PaymentMethodSelected('paymentId', new PaymentType('invoice', 86400, true));
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

    public function testTimeoutPurchaseWhenPaymentTimesOut()
    {
        $this->markTestSkipped('Test needs subscription support to work');
        $messages = $this->purchaseHasStarted();
        $messages[] = new PaymentRaised('paymentId', $this->actorId, Price::fromNetCost(300, 20));
        $this->helper->given($messages);
        $this->helper->when(new PaymentTimedOut('paymentId'));
        $this->helper->expect(new TicketReservationExpired($this->actorId, 'ticketId', 1));
    }

    public function testPaymentConfirmed()
    {
        $this->markTestSkipped('Test needs subscription support to work');
        $messages = $this->purchaseHasStarted();
        $messages[] = new PaymentRaised('paymentId', $this->actorId, Price::fromNetCost(300, 20));
        $messages[] = new PaymentMethodSelected('paymentId', new PaymentType('invoice', 86400, true));
        $this->helper->given($messages);
        $this->helper->when(new PaymentConfirmed('paymentId'));
        $this->helper->expect(new PurchaseCompleted($this->actorId));
    }

    public function testCheckoutPurchase()
    {
        $messages = $this->purchaseHasStarted();
        $this->helper->given($messages);
        $this->helper->when(new Checkout($this->actorId));
        $this->helper->expect(new PurchaseCheckedOut($this->actorId, Price::fromNetCost(10000, 20)));
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

    private function purchaseTicketsCommand(): PurchaseItems
    {
        return new PurchaseItems(
            'test@email.com',
            1,
            new Basket([new TicketQuantity(
                'ticketId',
                1,
                Price::fromNetCost(10000, 20)
            )], [])
        );
    }

    private function purchaseStartedByEvent(): PurchaseStartedBy
    {
        return new PurchaseStartedBy($this->actorId, 'test@email.com', 1, new Basket([new TicketQuantity(
            'ticketId',
            1,
            Price::fromNetCost(10000, 20)
        )], []));
    }

    private function checkPurchaseTimeoutEvent(): CheckPurchaseTimeout
    {
        return new CheckPurchaseTimeout($this->actorId);
    }

    private function outstandingPaymentCalculatedEvent(): OutstandingPaymentCalculated
    {
        return new OutstandingPaymentCalculated($this->actorId, Price::fromNetCost(10000, 20));
    }

    private function ticketsReservedEvent(): TicketsReserved
    {
        return new TicketsReserved($this->actorId, 'ticketId', 1);
    }
}
