<?php


namespace ConferenceTools\Attendance\Domain\Payment;

use ConferenceTools\Attendance\Domain\Payment\Command\CheckPaymentTimeout;
use ConferenceTools\Attendance\Domain\Payment\Command\ConfirmPayment;
use ConferenceTools\Attendance\Domain\Payment\Command\ProvidePaymentDetails;
use ConferenceTools\Attendance\Domain\Payment\Command\SelectPaymentMethod;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentConfirmed;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentError;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMethodSelected;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentPending;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentRaised;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentStarted;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentTimedOut;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCheckedOut;
use Phactor\Actor\AbstractActor;

class Payment extends AbstractActor
{
    const STATUS_RAISED = 'raised';
    const STATUS_STARTED = 'started';
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_TIMEDOUT = 'timedout';
    const STATUS_ERROR = 'error';

    private $amount;
    private $status = self::STATUS_RAISED;

    protected function handlePurchaseCheckedOut(PurchaseCheckedOut $event)
    {
        $this->fire(new PaymentRaised($this->id(), $event->getPurchaseId(), $event->getPaymentDue()));
    }

    protected function applyPaymentRaised(PaymentRaised $event)
    {
        $this->amount = $event->getPaymentDue();
    }

    protected function handleSelectPaymentMethod(SelectPaymentMethod $message)
    {
        $this->fire(new PaymentMethodSelected($this->id(), $message->getPaymentType()));

        $when = (new \DateTime())->add(
            new \DateInterval('PT' . $message->getPaymentType()->getPaymentTimeout() . 'S')
        );

        $this->schedule(new CheckPaymentTimeout($this->id()), $when);

        if ($message->getPaymentType()->requiresManualConfirmation()) {
            $this->fire(new PaymentPending($this->id()));
        } else {
            $this->fire(new PaymentStarted($this->id()));
        }
    }

    protected function handleProvidePaymentDetails(ProvidePaymentDetails $message)
    {
        //@TODO payment details provided event
        //@TODO add relevent information to payment details event (or is it just needed to trigger a status change?)
        if ($this->status === self::STATUS_STARTED) {
            $this->fire(new PaymentPending($this->id()));
        } elseif ($this->status === self::STATUS_CONFIRMED) {
            null;
            //@TODO tests for this branch
            //@TODO happens if stripe beats the client to making a request
        } else {
            //@TODO test this branch - occurs if payment details are provided after a timeout occurs
            //@TODO or if payment details are supplied twice
            $this->fire(new PaymentError($this->id()));
        }
    }

    protected function handleConfirmPayment(ConfirmPayment $message)
    {
        if ($this->status === self::STATUS_PENDING || $this->status === self::STATUS_STARTED) {
            $this->fire(new PaymentConfirmed($this->id()));
        } else {
            //@TODO test for error branch
            $this->fire(new PaymentError($this->id()));
        }
    }

    protected function applyPaymentPending(PaymentPending $message)
    {
        $this->status = self::STATUS_PENDING;
    }

    protected function applyPaymentStarted(PaymentStarted $message)
    {
        $this->status = self::STATUS_STARTED;
    }

    protected function applyPaymentConfirmed(PaymentConfirmed $message)
    {
        $this->status = self::STATUS_CONFIRMED;
    }

    protected function applyPaymentError(PaymentError $message)
    {
        $this->status = self::STATUS_ERROR;
    }

    protected function applyPaymentTimedOut(PaymentTimedOut $message)
    {
        $this->status = self::STATUS_TIMEDOUT;
    }

    protected function handleCheckPaymentTimeout(CheckPaymentTimeout $message)
    {
        //@TODO if encountered for a pending manual payment, can we timeout safely?
        switch ($this->status) {
            case self::STATUS_CONFIRMED:
            case self::STATUS_ERROR:
            case self::STATUS_TIMEDOUT:
                break;
            case self::STATUS_STARTED:
            case self::STATUS_RAISED:
                $this->fire(new PaymentTimedOut($this->id()));
                break;
            case self::STATUS_PENDING:
                $this->fire(new PaymentError($this->id()));
                break;
        }
    }
}