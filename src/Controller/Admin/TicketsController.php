<?php


namespace ConferenceTools\Attendance\Controller\Admin;


use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\Command\SendTicketEmail;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleSaleDate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleWithdrawDate;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Form\DateTimeForm;
use ConferenceTools\Attendance\Form\SendTicketsForm;
use ConferenceTools\Attendance\Form\TicketForm;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class TicketsController extends AppController
{
    private $taxRate = 20;

    public function indexAction()
    {
        $tickets = $this->repository(Ticket::class)->matching(Criteria::create());

        return new ViewModel(['tickets' => $tickets]);
    }

    public function newTicketAction()
    {
        $events = $this->indexBy($this->repository(Event::class)->matching(new Criteria()));

        $form = $this->form(TicketForm::class, ['eventOptions' => array_map(function(Event $event) { return $event->getName();}, $events)]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new ReleaseTicket(
                    $data['eventId'],
                    new Descriptor($data['name'], $data['description']),
                    $data['quantity'],
                    $this->makePrice($data['price'], $data['grossOrNet'])
                );
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/tickets');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Create new Ticket']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function withdrawAction()
    {
        $ticketId = $this->params()->fromRoute('ticketId');
        $form = $this->form(DateTimeForm::class, ['fieldLabel' => 'Withdraw from', 'submitLabel' => 'Withdraw']);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new ScheduleWithdrawDate($ticketId, new \DateTime($data['datetime']));
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/tickets');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Withdraw tickets']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function putOnSaleAction()
    {
        $ticketId = $this->params()->fromRoute('ticketId');
        $form = $this->form(DateTimeForm::class, ['fieldLabel' => 'On sale from', 'submitLabel' => 'Put on Sale']);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new ScheduleSaleDate($ticketId, new \DateTime($data['datetime']));
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/tickets');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Put tickets on sale']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function sendTicketEmailsAction()
    {
        $tickets = $this->getTickets();
        foreach ($tickets as $ticketId => $quantity) {
            $ticketOptions[$ticketId] = $tickets[$ticketId]->getEvent()->getName();
        }
        $form = $this->form(SendTicketsForm::class, ['ticketOptions' => $ticketOptions]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $delegates = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));
                foreach ($delegates as $delegate) {
                    /** @var Delegate $delegate */
                    if (!empty(array_intersect($delegate->getTickets(), $data['tickets']))) {
                        $command = new SendTicketEmail($delegate->getId());
                        $this->messageBus()->fire($command);
                    }
                }
                $this->flashMessenger()->addInfoMessage('Emails sent out');

                return $this->redirect()->toRoute('attendance-admin/tickets');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Send out ticket emails']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    private function makePrice($price, $grossOrNet)
    {
        if ($grossOrNet === 'gross') {
            return Price::fromGrossCost($price, $this->taxRate);
        }

        return Price::fromNetCost($price, $this->taxRate);
    }

}