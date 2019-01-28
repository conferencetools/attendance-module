<?php


namespace ConferenceTools\Attendance\Controller\Admin;


use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Discounting\Command\CreateDiscount;
use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountType;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use ConferenceTools\Attendance\Domain\Ticketing\Command\PutOnSale;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Command\WithdrawFromSale;
use ConferenceTools\Attendance\Domain\Ticketing\Event;
use ConferenceTools\Attendance\Domain\Ticketing\Money;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\TaxRate;
use ConferenceTools\Attendance\Form\DiscountForm;
use ConferenceTools\Attendance\Form\TicketForm;
use Doctrine\Common\Collections\Criteria;
use Zend\Form\Element\DateTime;
use Zend\View\Model\ViewModel;

class DiscountsController extends AppController
{
    private $taxRate;

    public function __construct(/*TaxRate $taxRate*/)
    {
        $this->taxRate = new TaxRate(20);
    }

    public function indexAction()
    {
        $discounts = $this->repository(DiscountType::class)->matching(Criteria::create());

        return new ViewModel(['discounts' => $discounts]);
    }

    public function newDiscountAction()
    {
        $tickets = $this->repository(Ticket::class)->matching(Criteria::create());
        foreach ($tickets as $ticket) {
            /** @var Ticket $ticket */
            $ticketOptions[$ticket->getId()] = $ticket->getEvent()->getName();
        }

        $form = $this->form(DiscountForm::class, ['tickets' => $ticketOptions]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $command = new CreateDiscount(
                    $data['name'],
                    $this->makeAvailableDates($data['from'], $data['until']),
                    $this->makeDiscount($data)
                );
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/discounts');
            }
        }

        return new ViewModel(['form' => $form]);
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

    private function makeDiscount($data): Discount
    {
        switch ($data['type']) {
            case 'percentage':
                $discount = Discount::percentage($data['percent'], ...$data['ticketIds'] ?? []);
                break;
            case 'perTicket':
                $discount = Discount::perTicket($this->makePrice($data['price'], $data['grossOrNet']), ...$data['ticketIds'] ?? []);
                break;
            case 'perPurchase':
                $discount = Discount::perPurchase($this->makePrice($data['price'], $data['grossOrNet']), ...$data['ticketIds'] ?? []);
                break;
        }

        return $discount;
    }

}