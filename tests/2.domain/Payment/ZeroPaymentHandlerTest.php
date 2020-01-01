<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment;

use ConferenceTools\Attendance\Domain\Payment\Command\ConfirmPayment;
use ConferenceTools\Attendance\Domain\Payment\Command\SelectPaymentMethod;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentConfirmed;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentRaised;
use ConferenceTools\Attendance\Domain\Payment\MessageSubscriptions;
use ConferenceTools\Attendance\Domain\Payment\Payment;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Payment\ZeroPaymentHandler;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Phactor\Identity\Generator;
use Phactor\Message\Bus;
use Phactor\Message\MessageFirer;
use Phactor\Test\ActorHelper;
use Phactor\Test\TesterFactory;

/**
 * @covers \ConferenceTools\Attendance\Domain\Payment\ZeroPaymentHandler
 */
class ZeroPaymentHandlerTest extends \Codeception\Test\Unit
{
    /**
     * @var TesterFactory
     */
    private $testerFactory;

    public function _before()
    {
        $this->testerFactory = new TesterFactory();
    }

    public function testZeroPaymentIsMarkedAsPaid()
    {
        $factory = function(Bus $bus, Generator $generator) {
            $messageFirer = new MessageFirer($generator, $bus);
            return new ZeroPaymentHandler($messageFirer);
        };

        $tester = $this->testerFactory->handler($factory, new MessageSubscriptions());
        $tester->when(new PaymentRaised('paymentId', 'purchaseId', Price::fromNetCost(0, 20)));
        $tester->expect(new SelectPaymentMethod('paymentId', new PaymentType('no-payment', 60, true)));
        $tester->expect(new ConfirmPayment('paymentId'));
    }

    public function testNonZeroPaymentIsIgnored()
    {
        $factory = function(Bus $bus, Generator $generator) {
            $messageFirer = new MessageFirer($generator, $bus);
            return new ZeroPaymentHandler($messageFirer);
        };

        $tester = $this->testerFactory->handler($factory, new MessageSubscriptions());
        $tester->when(new PaymentRaised('paymentId', 'purchaseId', Price::fromNetCost(100, 20)));
        $tester->expectNoMoreMessages();
    }
}
