<?php

namespace ConferenceTools\Attendance\PaymentProvider;

use Cartalyst\Stripe\Stripe as StripeClient;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMethodSelected;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use Phactor\Message\DomainMessage;
use Phactor\ReadModel\Repository;
use Zend\View\Model\ViewModel;

class StripeViewProvider
{
    private $stripePaymentRepository;

    public function __construct(Repository $stripePaymentRepository)
    {
        $this->stripePaymentRepository = $stripePaymentRepository;
    }

    public function getView(Purchase $purchase, Payment $payment): ViewModel
    {
        $stripePayment = $this->stripePaymentRepository->get($payment->getId());

        $viewModel = new ViewModel(['purchase' => $purchase, 'payment' => $payment, 'stripePayment' => $stripePayment]);
        $viewModel->setTemplate('attendance/purchase/stripe');
        return $viewModel;
    }
}