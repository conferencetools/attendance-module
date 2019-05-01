<?php


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class EventsCest
{
    public function _before(FunctionalTester $I)
    {
        /** @var Symfony\Component\Console\Application $application */
        $application = $I->grabServiceFromContainer('doctrine.cli');
        $application->setAutoExit(false);
        if (file_exists(__DIR__ . '/../_data/db.sqlite')) {
            unlink(__DIR__ . '/../_data/db.sqlite');
        }
        $application->run(new ArrayInput(['command' => 'orm:schema-tool:create']), new NullOutput());
    }

    // tests

    public function testNoEvent(FunctionalTester $I)
    {
        $I->amOnPage('/admin/events');
        $I->see('You haven\'t created any events yet.');
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
