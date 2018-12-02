<?php


namespace ConferenceTools\Attendance\Controller\Admin;


use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
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
            }
        }

        return new ViewModel(['form' => $form]);
    }

    private function makeAvailableDates(string $from, string $until)
    {
        if ($from === '') {
            if ($until === '') {
                return AvailabilityDates::always();
            }

            return AvailabilityDates::until(\DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $until));
        }

        if ($until === '') {
            return AvailabilityDates::after(\DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $from));
        }

        return AvailabilityDates::between(
            \DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $from),
            \DateTime::createFromFormat(DateTime::DATETIME_FORMAT, $until)
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