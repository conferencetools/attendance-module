<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment;

use ConferenceTools\Attendance\Domain\Payment\Command;
use ConferenceTools\Attendance\Domain\Payment\Event;
use ConferenceTools\Attendance\Domain\Payment\Payment;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCheckedOut;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Phactor\Test\ActorHelper;

/**
 * @covers \ConferenceTools\Attendance\Domain\Payment\Payment
 */
class PaymentTest extends \Codeception\Test\Unit
{
    /** @var ActorHelper */
    private $helper;
    private $actorId = '';

    public function _before()
    {
        $this->helper = new ActorHelper(Payment::class);
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testPurchaseCheckedOutCreatesPayment()
    {
        $this->helper->when(new PurchaseCheckedOut('purchase', Price::fromNetCost(500, 20)));

        $this->helper->expect(new Event\PaymentRaised($this->actorId, 'purchase', Price::fromNetCost(500, 20)));
        $this->helper->expectNoMoreMessages();
    }

    public function testManualPaymentMethod()
    {
        $this->helper->given($this->paymentRaised());

        $this->helper->when(new Command\SelectPaymentMethod($this->actorId, new PaymentType('test', 300, true)));

        $this->helper->expect(new Event\PaymentMethodSelected($this->actorId, new PaymentType('test', 300, true)));
        $this->helper->expect(new Event\PaymentPending($this->actorId));
        $this->helper->expect(new Command\CheckPaymentTimeout($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testAsyncPaymentMethod()
    {
        $this->helper->given($this->paymentRaised());

        $this->helper->when(new Command\SelectPaymentMethod($this->actorId, new PaymentType('test', 300, false)));

        $this->helper->expect(new Event\PaymentMethodSelected($this->actorId, new PaymentType('test', 300, false)));
        $this->helper->expect(new Event\PaymentStarted($this->actorId));
        $this->helper->expect(new Command\CheckPaymentTimeout($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testProvidePaymentDetails()
    {
        $this->helper->given($this->asyncPaymentStarted());
        $this->helper->when(new Command\ProvidePaymentDetails($this->actorId));

        $this->helper->expect(new Event\PaymentPending($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testProvidePaymentDetailsThenConfirmPayment()
    {
        $messages = $this->asyncPaymentStarted();
        $messages[] = new Command\ProvidePaymentDetails($this->actorId);
        $messages[] = new Event\PaymentPending($this->actorId);
        $this->helper->given($messages);
        $this->helper->when(new Command\ConfirmPayment($this->actorId));

        $this->helper->expect(new Event\PaymentConfirmed($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testConfirmPaymentDetails()
    {
        $this->helper->given($this->asyncPaymentStarted());
        $this->helper->when(new Command\ConfirmPayment($this->actorId));

        $this->helper->expect(new Event\PaymentConfirmed($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testPaymentTimeout()
    {
        $this->helper->given($this->asyncPaymentStarted());
        $this->helper->when(new Command\CheckPaymentTimeout($this->actorId));

        $this->helper->expect(new Event\PaymentTimedOut($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    public function testPaymentTimeoutAfterConfirmation()
    {
        $messages = $this->asyncPaymentStarted();
        $messages[] = new Command\ConfirmPayment($this->actorId);
        $messages[] = new Event\PaymentConfirmed($this->actorId);
        $this->helper->given($messages);
        $this->helper->when(new Command\CheckPaymentTimeout($this->actorId));

        $this->helper->expectNoMoreMessages();
    }

    protected function paymentRaised(): array
    {
        return [
            new PurchaseCheckedOut('purchase', Price::fromNetCost(500, 20)),
            new Event\PaymentRaised($this->actorId, 'purchase', Price::fromNetCost(500, 20))
        ];
    }

    protected function asyncPaymentStarted()
    {
        return array_merge(
            $this->paymentRaised(),
            [
                new Command\SelectPaymentMethod($this->actorId, new PaymentType('test', 300, false)),
                new Event\PaymentMethodSelected($this->actorId, new PaymentType('test', 300, false)),
                new Event\PaymentStarted($this->actorId),
                new Command\CheckPaymentTimeout($this->actorId),
            ]
        );
    }
}