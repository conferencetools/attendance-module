<?php

namespace ConferenceTools\Attendance\Domain\Payment;

use ConferenceTools\Attendance\Domain\Payment\Command\ConfirmPayment;
use ConferenceTools\Attendance\Domain\Payment\Command\SelectPaymentMethod;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\Message\FiresMessages;

class ZeroPaymentHandler implements Handler
{
    private $messageBus;

    public function __construct(FiresMessages $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function handle(DomainMessage $message)
    {
        $event = $message->getMessage();
        if (!($event instanceof Event\PaymentRaised)) {
            return;
        }

        $paymentDue = $event->getPaymentDue();

        if ($paymentDue->equals(Price::fromNetCost(0, $paymentDue->getTaxRate()))) {
            $this->messageBus->fire(new SelectPaymentMethod($event->getId(), new PaymentType('no-payment', 60, true)));
            $this->messageBus->fire(new ConfirmPayment($event->getId()));
        }
    }
}