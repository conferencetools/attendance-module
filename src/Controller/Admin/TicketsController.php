<?php


namespace ConferenceTools\Attendance\Controller\Admin;


use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\Command\SendTicketEmail;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Discounting\Command\CreateDiscount;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Command\PutOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Command\WithdrawFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
use ConferenceTools\Attendance\Form\ConfirmationForm;
use ConferenceTools\Attendance\Form\TicketForm;
use Doctrine\Common\Collections\Criteria;
use Zend\Form\Element\DateTime;
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
        $form = $this->form(TicketForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new ReleaseTicket(
                    new Event($data['code'], $data['name'], $data['description']),
                    $data['quantity'],
                    $this->makeAvailableDates($data['from'], $data['until']),
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
        $form = $this->form(ConfirmationForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $delegates = $this->repository(Delegate::class)->matching(Criteria::create());
                if ($data['confirm'] !== null) {
                    foreach ($delegates as $delegate) {
                        /** @var Delegate $delegate */
                        $command = new SendTicketEmail($delegate->getId());
                        $this->messageBus()->fire($command);
                    }
                    $this->flashMessenger()->addInfoMessage('Emails sent out');
                } else {
                    $this->flashMessenger()->addInfoMessage('Action cancelled');
                }

                return $this->redirect()->toRoute('attendance-admin/tickets');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'send out ticket emails to all delegates']);
        $viewModel->setTemplate('attendance/admin/confirmation-form');
        return $viewModel;
    }

    private function makeAvailableDates(string $from, string $until)
    {
        $timezone = new \DateTimeZone('UTC');
        if ($from === '') {
            if ($until === '') {
                return AvailabilityDates::always();
            }

            return AvailabilityDates::until(\DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $until, $timezone));
        }

        if ($until === '') {
            return AvailabilityDates::after(\DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $from, $timezone));
        }

        return AvailabilityDates::between(
            \DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $from, $timezone),
            \DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $until, $timezone)
        );
    }

    private function makePrice($price, $grossOrNet)
    {
        if ($grossOrNet === 'gross') {
            return Price::fromGrossCost(new Money($price), $this->taxRate);
        }

        return Price::fromNetCost(new Money($price), $this->taxRate);
    }

}