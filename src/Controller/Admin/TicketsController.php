<?php


namespace ConferenceTools\Attendance\Controller\Admin;


use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\Command\SendTicketEmail;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Ticketing\Command\PutOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Command\WithdrawFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
use ConferenceTools\Attendance\Form\SendTicketsForm;
use ConferenceTools\Attendance\Form\TicketForm;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class TicketsController extends AppController
{
    private $taxRate;

    public function __construct(/*TaxRate $taxRate*/)
    {
        $this->taxRate = new TaxRate(20);
    }

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

        return new ViewModel(['form' => $form]);
    }

    public function withdrawAction()
    {
        $ticketId = $this->params()->fromRoute('ticketId');
        $command = new WithdrawFromSale($ticketId);
        $this->messageBus()->fire($command);

        return $this->redirect()->toRoute('attendance-admin/tickets');
    }

    public function putOnSaleAction()
    {
        $ticketId = $this->params()->fromRoute('ticketId');
        $command = new PutOnSale($ticketId);
        $this->messageBus()->fire($command);

        return $this->redirect()->toRoute('attendance-admin/tickets');
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
            return Price::fromGrossCost(new Money($price), $this->taxRate);
        }

        return Price::fromNetCost(new Money($price), $this->taxRate);
    }

}