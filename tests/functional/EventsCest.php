<?php


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class EventsCest
{
    public function testNoEvent(FunctionalTester $I)
    {
        $I->amOnPage('/admin/events');
        $I->see('You haven\'t created any events yet.');
        $I->seeLink('Events', '/admin/events');
        $I->seeLink('Add', '/admin/events/new');
    }

    public function testCreateEvent(FunctionalTester $I)
    {
        $I->amOnPage('/admin/events/new');
        $name = 'event' . uniqid();
        $description = 'description' . uniqid();
        $I->submitForm(
            'form',
            [
                'name' => $name,
                'description' => $description,
                'capacity' => 50,
                'startsOn' => (new \DateTime())->add(new \DateInterval('P1D'))->format('Y-m-d\TH:iP'),
                'endsOn' => (new \DateTime())->add(new \DateInterval('P2D'))->format('Y-m-d\TH:iP'),
            ]
        );

        $I->seeCurrentUrlEquals('/admin/events');
        $I->see($name);
        $I->see($description);
    }
}
