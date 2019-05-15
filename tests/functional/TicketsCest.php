<?php


use ConferenceTools\Attendance\Domain\Ticketing\Command\CreateEvent;
use ConferenceTools\Attendance\Domain\Ticketing\Command\ReleaseTicket;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use Phactor\Identity\Generator;
use Phactor\Message\Bus;
use Phactor\Message\MessageFirer;

class TicketsCest
{
    /** @var MessageFirer */
    private $bus;

    public function _before(FunctionalTester $I)
    {
        /** @var Bus $bus */
        $messageBus = $I->grabServiceFromContainer(Bus::class);
        $identityGenerator = $I->grabServiceFromContainer(Generator::class);

        $this->bus = new MessageFirer($identityGenerator, $messageBus);
    }

    public function testNoTickets(FunctionalTester $I)
    {
        $I->amOnPage('/admin/tickets');
        $I->see('You haven\'t created any tickets yet.');
        $I->seeLink('Tickets', '/admin/tickets');
        $I->seeLink('Add', '/admin/tickets/new');
    }

    public function testCreateTicket(FunctionalTester $I)
    {
        $eventId = $this->createEvent($I);

        $I->amOnPage('/admin/tickets/new');
        $name = 'event' . uniqid();
        $description = 'description' . uniqid();
        $I->submitForm(
            'form',
            [
                'eventId'=> $eventId,
                'name' => $name,
                'description' => $description,
                'quantity' => 50,
                'grossOrNet' => 'gross',
                'price' => 300,
            ]
        );

        $I->seeCurrentUrlEquals('/admin/tickets');
        $I->see($name);
        $I->see($description);
    }

    public function testPutTicketOnSaleNow(FunctionalTester $I)
    {
        $eventId = $this->createEvent($I);
        $ticketId = $this->createTicket($I, $eventId);


        $I->amOnPage('/admin/tickets');
        $I->click('Put on Sale');
        $I->seeCurrentUrlEquals('/admin/tickets/put-on-sale/' . $ticketId);

        $onSaleFrom = (new \DateTime())->sub(new \DateInterval('P1D'));

        $I->submitForm(
            'form',
            [
                'datetime' => $onSaleFrom->format('Y-m-d\TH:iP'),
            ]
        );

        $I->seeCurrentUrlEquals('/admin/tickets');

        $this->bus->fire(new \ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBePutOnSale($ticketId, $onSaleFrom));

        $I->amOnPage('/admin/tickets');
        $I->seeLink('Withdraw');
    }

    public function testWithdrawTicketsNow(FunctionalTester $I)
    {
        $onSaleFrom = (new \DateTime())->sub(new \DateInterval('P2D'));
        $withdrawFrom = (new \DateTime())->sub(new \DateInterval('P1D'));
        $eventId = $this->createEvent($I);
        $ticketId = $this->createTicket($I, $eventId);
        $this->bus->fire(new \ConferenceTools\Attendance\Domain\Ticketing\Command\ScheduleSaleDate($ticketId, $onSaleFrom));
        $this->bus->fire(new \ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBePutOnSale($ticketId, $onSaleFrom));

        $I->amOnPage('/admin/tickets');
        $I->click('Withdraw');
        $I->seeCurrentUrlEquals('/admin/tickets/withdraw/' . $ticketId);

        $I->submitForm(
            'form',
            [
                'datetime' => $withdrawFrom->format('Y-m-d\TH:iP'),
            ]
        );

        $I->seeCurrentUrlEquals('/admin/tickets');

        $this->bus->fire(new \ConferenceTools\Attendance\Domain\Ticketing\Command\ShouldTicketBeWithdrawn($ticketId, $withdrawFrom));

        $I->amOnPage('/admin/tickets');
        $I->seeLink('Put on Sale');
    }

    private function createEvent(FunctionalTester $I): string
    {
        $events = $this->bus->fire(new CreateEvent('event', 'event', 10, new \DateTime(), new \DateTime()));
        $eventId = $events[1]->getMessage()->getId();
        return $eventId;
    }

    private function createTicket(FunctionalTester $I, string $eventId): string
    {
        $events = $this->bus->fire(new ReleaseTicket(
            $eventId,
            new Descriptor('name', 'description'),
            100,
            Price::fromNetCost(50, 20)
        ));
        $ticketId = $events[1]->getMessage()->getId();
        return $ticketId;
    }
}
