<?php

namespace ConferenceTools\Attendance\PaymentProvider;

use Cartalyst\Stripe\Stripe as StripeClient;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMethodSelected;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;
use Zend\View\Model\ViewModel;

class StripePaymentHandler implements Handler
{
    private $paymentRepository;
    private $stripePaymentRepository;
    private $stripeClient;
    private $currency = 'GBP';

    public function __construct(Repository $paymentRepository, Repository $stripePaymentRepository, StripeClient $stripeClient)
    {
        $this->paymentRepository = $paymentRepository;
        $this->stripePaymentRepository = $stripePaymentRepository;
        $this->stripeClient = $stripeClient;
    }

    public function handle(DomainMessage $domainMessage)
    {
        /** @var PaymentMethodSelected $message */
        $message = $domainMessage->getMessage();
        if ($message->getPaymentType()->getName() !== 'stripe') {
            return;
        }

        /** @var Payment $payment */
        $payment = $this->paymentRepository->get($message->getId());

        $response = $this->stripeClient->paymentIntents()->create([
            "amount" => $payment->getAmount()->getGross(),
            "currency" => $this->currency,
            'metadata' => [
                'paymentId' => $payment->getId(),
                'purchaseId' => $payment->getPurchaseId(),
            ]
        ]);

        $entity = new StripePayment($payment->getId(), $response['id'], $response['client_secret']);
        $this->stripePaymentRepository->add($entity);
        $this->stripePaymentRepository->commit();
    }
}