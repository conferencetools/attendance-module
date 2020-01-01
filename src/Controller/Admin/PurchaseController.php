<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Payment\Command\ConfirmPayment;
use ConferenceTools\Attendance\Domain\Payment\Command\SelectPaymentMethod;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentRaised;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use ConferenceTools\Attendance\Domain\Purchasing\Basket;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\Checkout;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseItems;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Form\DelegatesForm;
use ConferenceTools\Attendance\Form\NumberOfDelegates;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class PurchaseController extends AppController
{
    public function indexAction()
    {
        $purchases = $this->repository(Purchase::class)->matching(Criteria::create());
        return new ViewModel(['purchases' => $purchases]);
    }

    public function viewAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');
        $purchase = $this->repository(Purchase::class)->get($purchaseId);
        if ($purchase === null) {
            $this->flashMessenger()->addWarningMessage('Purchase not found');
            return $this->redirect()->toRoute('attendance-admin/purchase');
        }

        $payments = $this->repository(Payment::class)->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $purchaseId)));
        $delegates = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $purchaseId)));

        return new ViewModel(['purchase' => $purchase, 'payments' => $payments, 'delegates' => $delegates, 'tickets' => $this->getTickets(false)]);
    }

    public function createAction()
    {
        $form = $this->form(NumberOfDelegates::class);
        $form->setAttribute('action', $this->url()->fromRoute('attendance-admin/purchase/delegates'));
        $form->setAttribute('method', 'GET');

        $viewModel = new ViewModel(['form' => $form]);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function paymentReceivedAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');
        $paymentId = $this->params()->fromRoute('paymentId');

        /** @var Payment $payment */
        $payment = $this->repository(Payment::class)->get($paymentId);

        if ($payment->isPending()) {
            $this->messageBus()->fire(new ConfirmPayment($paymentId));
            $this->flashMessenger()->addSuccessMessage('Payment marked as received');
        }

        return $this->redirect()->toRoute('attendance-admin/purchase/view', ['purchaseId' => $purchaseId]);
    }

    public function delegatesAction()
    {
        $form = $this->form(NumberOfDelegates::class);
        $form->setData($this->params()->fromQuery());
        if (!$form->isValid()) {
            $this->flashMessenger()->addErrorMessage('Please supply a positive number of delegates and a valid email address');
            return $this->redirect()->toRoute('attendance-admin/purchase/create');
        }
        $queryData = $form->getData();
        $delegates = $queryData['delegates'];
        $email = $queryData['email'];
        $delegateType = $queryData['delegateType'];

        $tickets = $this->getTickets();
        foreach ($tickets as $ticketId => $quantity) {
            $ticketOptions[$ticketId] = $tickets[$ticketId]->getDescriptor()->getName();
        }

        $form = $this->form(DelegatesForm::class, [
            'ticketOptions' => $ticketOptions,
            'maxDelegates' => $delegates
        ]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $purchaseTickets = [];
                for ($i = 0; $i < $delegates; $i++) {
                    $delegate = $data['delegate_' . $i];

                    if (empty($delegate['tickets'])) {
                        continue;
                    }

                    foreach ($delegate['tickets'] as $ticketId) {
                        $purchaseTickets[$ticketId] +=1;
                    }
                }

                foreach ($purchaseTickets as $ticketId => $quantity) {
                    $selectedTickets[] = new TicketQuantity(
                        $ticketId,
                        $quantity,
                        $tickets[$ticketId]->getPrice()
                    );
                }

                $messages = $this->messageBus()->fire(new PurchaseItems($email, (int) $delegates, new Basket($selectedTickets, [])));
                $purchaseId = $this->messageBus()->firstInstanceOf(TicketsReserved::class, ...$messages)->getId();

                for ($i = 0; $i < $delegates; $i++) {
                    $delegate = $data['delegate_' . $i];

                    if (empty($delegate['tickets'])) {
                        continue;
                    }

                    $dietaryRequirements = new DietaryRequirements($delegate['preference'], $delegate['allergies']);

                    $command = new RegisterDelegate(
                        $purchaseId,
                        $delegate['name'],
                        empty($delegate['email']) ? $queryData['email'] : $delegate['email'],
                        $delegate['company'],
                        $dietaryRequirements,
                        $delegate['requirements'],
                        $delegateType
                    );

                    $messages = $this->messageBus()->fire($command);
                    $delegateId = $this->messageBus()->firstInstanceOf(DelegateRegistered::class, ...$messages)->getId();

                    foreach ($delegate['tickets'] as $ticketId) {
                        $command = new AllocateTicketToDelegate($delegateId, $purchaseId, $ticketId);
                        $this->messageBus()->fire($command);
                    }
                }
                $messages = $this->messageBus()->fire(new Checkout($purchaseId));
                $paymentId = $this->messageBus()->firstInstanceOf(PaymentRaised::class, ...$messages)->getId();
                $this->messageBus()->fire(new SelectPaymentMethod($paymentId, new PaymentType('manual', 86400*60, true)));
                return $this->redirect()->toRoute('attendance-admin/purchase/view', ['purchaseId' => $purchaseId]);
            }
        }

        return new ViewModel(['form' =>  $form, 'tickets' => $tickets, 'delegates' => $delegates]);
    }

}
