<?php

namespace ConferenceTools\AttendanceTest\Domain\Payment;

use ConferenceTools\Attendance\Domain\Payment\Command;
use ConferenceTools\Attendance\Domain\Payment\Event;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Payment\Projector;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Phactor\Test\ActorHelper;
use Phactor\Test\ProjectorHelper;

/**
 * @covers \ConferenceTools\Attendance\Domain\Payment\Projector
 */
class ProjectorTest extends \Codeception\Test\Unit
{
    /** @var ProjectorHelper */
    private $helper;

    public function _before()
    {
        $this->helper = ProjectorHelper::fromClassName(Projector::class);
    }

    public function testPaymentRaised()
    {
        $id = 'paymentId';
        $purchaseId = 'purchaseId';
        $paymentDue = Price::fromNetCost(300, 20);
        $this->helper->when(new Event\PaymentRaised($id, $purchaseId, $paymentDue));
        $this->helper->expect(new Payment($id, $purchaseId, $paymentDue));
    }

    public function testPaymentMethodSelected()
    {
        $id = '0';
        $purchaseId = 'purchaseId';
        $paymentDue = Price::fromNetCost(300, 20);
        $paymentType = new PaymentType('manual', 86400, true);

        $this->helper->given(new Payment($id, $purchaseId, $paymentDue));

        $this->helper->when(new Event\PaymentMethodSelected($id, $paymentType));

        $expected = new Payment($id, $purchaseId, $paymentDue);
        $expected->paymentMethodProvided($paymentType);

        $this->helper->expect($expected);
    }

    /** @dataProvider provideStatuses */
    public function testPaymentStatuses($message, $expected)
    {
        $id = '0';
        $purchaseId = 'purchaseId';
        $paymentDue = Price::fromNetCost(300, 20);
        $this->helper->given(new Payment($id, $purchaseId, $paymentDue));
        $this->helper->when($message);
        $this->helper->expect($expected);
    }

    public function provideStatuses()
    {
        $id = '0';
        $purchaseId = 'purchaseId';
        $paymentDue = Price::fromNetCost(300, 20);
        $template = new Payment($id, $purchaseId, $paymentDue);

        $confirmed = clone $template;
        $confirmed->setStatus('confirmed');

        $pending = clone $template;
        $pending->setStatus('pending');

        $timedOut = clone $template;
        $timedOut->setStatus('timedout');

        $started = clone $template;
        $started->setStatus('started');

        return [
            [new Event\PaymentConfirmed('0'), $confirmed],
            [new Event\PaymentPending('0'), $pending],
            [new Event\PaymentTimedOut('0'), $timedOut],
            [new Event\PaymentStarted('0'), $started],
        ];
    }
}