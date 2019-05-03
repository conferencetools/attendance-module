<?php


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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
        $I->amOnPage('/admin/tickets/new');
        $name = 'event' . uniqid();
        $description = 'description' . uniqid();
        $I->submitForm(
            'form',
            [
                'code'=> 'eventcode',
                'name' => $name,
                'description' => $description,
                'quantity' => 50,
                'from' => (new \DateTime())->add(new \DateInterval('P1D'))->format('Y-m-d\TH:iP'),
                'until' => (new \DateTime())->add(new \DateInterval('P2D'))->format('Y-m-d\TH:iP'),
                'grossOrNet' => 'gross',
                'price' => 300,
            ]
        );

        $I->seeCurrentUrlEquals('/admin/tickets');
        $I->see($name);
        $I->see($description);
    }
}
