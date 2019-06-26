<?php

namespace ConferenceTools\AttendanceTest\Service;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Service\TicketService;
use ConferenceTools\Attendance\Service\TicketValidationFailed;
use Phactor\ReadModel\InMemoryRepository;
use Phactor\ReadModel\Repository;

class TicketServiceTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    /** @dataProvider provideValidateTicketQuantity */
    public function testValidateTicketQuantity(array $quantities, bool $success, string $reason)
    {
        $ticket = new Ticket('0', 'event', new Descriptor('name', 'desc'), 5, Price::fromNetCost(100, 12));
        $ticketOnSale = new Ticket('1', 'event', new Descriptor('name', 'desc'), 5, Price::fromNetCost(100, 12));
        $ticketOnSale->onSale();

        $ticketsRepository = new InMemoryRepository();
        $ticketsRepository->add($ticket);
        $ticketsRepository->add($ticketOnSale);

        $event = new Event('event', new Descriptor('name', 'desc'), 3, new \DateTime(), new \DateTime());

        $eventsRepository = new InMemoryRepository();
        $eventsRepository->add($event);


        $sut = new TicketService(
            $ticketsRepository,
            $eventsRepository
        );

        $result = $sut->validateTicketQuantity($quantities);

        $this->tester->assertTrue($success || $result instanceof TicketValidationFailed);
        $this->tester->assertEquals($reason, $result->getReason());
    }

    public function provideValidateTicketQuantity()
    {
        return [
            [['0' => 1], false, 'One or more of the tickets you selected has sold out or you have selected more than the quantity remaining'],
            [['1' => 1], true, ''],
            [[], false, 'Please select at least one ticket to purchase'],
            [['1' => 0], false, 'Please select at least one ticket to purchase'],
            [['1' => 7], false, 'One or more of the tickets you selected has sold out or you have selected more than the quantity remaining'],
            [['1' => 4], false, 'The tickets you have selected would put the event over capacity, please reduce the number of tickets you have selected'],
        ];
    }

    /** @dataProvider provideGetTickets */
    public function testGetTickets(Repository $ticketsRepository, bool $onSale, array $expected)
    {
        $sut = new TicketService(
            $ticketsRepository,
            new InMemoryRepository()
        );

        $result = $sut->getTickets($onSale);

        $this->tester->assertEquals($expected, $result);
    }

    public function provideGetTickets()
    {
        $ticket = new Ticket('0', 'event', new Descriptor('name', 'desc'), 25, Price::fromNetCost(100, 12));
        $ticketOnSale = new Ticket('1', 'event', new Descriptor('name', 'desc'), 25, Price::fromNetCost(100, 12));
        $ticketOnSale->onSale();

        $repository = new InMemoryRepository();
        $repository->add($ticket);
        $repository->add($ticketOnSale);

        return [
            [new InMemoryRepository(), true, []],
            [$repository, false, [$ticket, $ticketOnSale]],
            [$repository, true, [1 => $ticketOnSale]],
        ];
    }

}
