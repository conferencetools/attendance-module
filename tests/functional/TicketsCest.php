<?php


use Phactor\Identity\Generator;
use Phactor\Message\Bus;
use Phactor\Message\MessageFirer;

class TicketsCest
{
    public function testNoTickets(FunctionalTester $I)
    {
        $I->amOnPage('/admin/tickets');
        $I->see('You haven\'t created any tickets yet.');
        $I->seeLink('Tickets', '/admin/tickets');
        $I->seeLink('Add', '/admin/tickets/new');
    }

    public function testCreateTicket(FunctionalTester $I)
    {
        /** @var Bus $bus */
        $messageBus = $I->grabServiceFromContainer(Bus::class);
        $identityGenerator = $I->grabServiceFromContainer(Generator::class);

        $bus = new MessageFirer($identityGenerator, $messageBus);
        $events = $bus->fire(new \ConferenceTools\Attendance\Domain\Ticketing\Command\CreateEvent('event', 'event', 10, new \DateTime(), new \DateTime()));
        $eventId = $events[1]->getMessage()->getId();

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
}
