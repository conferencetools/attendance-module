<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseTickets;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Form\DelegatesForm;
use ConferenceTools\Attendance\Form\NumberOfDelegates;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class PurchaseController extends AppController
{

    public function indexAction()
    {
        $form = $this->form(NumberOfDelegates::class);

        return new ViewModel(['form' => $form]);
    }

    public function delegatesAction()
    {
        $form = $this->form(NumberOfDelegates::class);
        $form->setData($this->params()->fromQuery());
        if (!$form->isValid()) {
            $this->flashMessenger()->addErrorMessage('Please supply a positive number of delegates and a valid email address');
            return $this->redirect()->toRoute('attendance-admin/purchase');
        }
        $queryData = $form->getData();
        $delegates = $queryData['delegates'];
        $email = $queryData['email'];
        $delegateType = $queryData['delegateType'];

        $tickets = $this->getTickets();
        foreach ($tickets as $ticketId => $quantity) {
            $ticketOptions[$ticketId] = $tickets[$ticketId]->getEvent()->getName();
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
                        $tickets[$ticketId]->getEvent(),
                        $quantity,
                        $tickets[$ticketId]->getPrice()
                    );
                }

                $messages = $this->messageBus()->fire(new PurchaseTickets($email, (int) $delegates, ...$selectedTickets));
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
                        $delegate['email'],
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
                $this->messageBus()->fire(new PaymentMade($purchaseId));
                return $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $purchaseId]);
            }
        }

        return new ViewModel(['form' =>  $form, 'tickets' => $tickets, 'delegates' => $delegates]);
    }

}
