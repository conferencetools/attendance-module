<?php


namespace ConferenceTools\Attendance\Domain\Payment;


use ConferenceTools\Attendance\Domain\Payment\Event\PaymentConfirmed;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentError;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMethodSelected;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentPending;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentRaised;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentStarted;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentTimedOut;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment as PaymentReadModel;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;

class Projector implements Handler
{
    private $paymentRepository;

    public function __construct(Repository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function handle(DomainMessage $message)
    {
        $event = $message->getMessage();
        switch (true) {
            case $event instanceof PaymentRaised:
                $this->paymentRaised($event);
                break;
            case $event instanceof PaymentConfirmed:
                $this->paymentStatus($event->getId(), Payment::STATUS_CONFIRMED);
                break;
            case $event instanceof PaymentError:
                break;
            case $event instanceof PaymentMethodSelected:
                $this->paymentMethodSelected($event);
                break;
            case $event instanceof PaymentPending:
                $this->paymentStatus($event->getId(), Payment::STATUS_PENDING);
                break;
            case $event instanceof PaymentStarted:
                $this->paymentStatus($event->getId(), Payment::STATUS_STARTED);
                break;
            case $event instanceof PaymentTimedOut:
                $this->paymentStatus($event->getId(), Payment::STATUS_TIMEDOUT);
                break;
        }

        $this->paymentRepository->commit();
    }

    private function paymentRaised(PaymentRaised $message): void
    {
        $payment = new PaymentReadModel($message->getId(), $message->getPurchaseId(), $message->getPaymentDue());
        $this->paymentRepository->add($payment);
    }

    private function paymentMethodSelected(PaymentMethodSelected $message): void
    {
        $payment = $this->fetchPayment($message->getId());
        $payment->paymentMethodProvided($message->getPaymentType());
    }

    private function paymentStatus(string $id, string $status): void
    {
        $payment = $this->fetchPayment($id);
        $payment->setStatus($status);
    }

    private function fetchPayment(string $id): PaymentReadModel
    {
        return $this->paymentRepository->get($id);
    }
}