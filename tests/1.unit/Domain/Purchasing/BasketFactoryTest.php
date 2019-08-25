<?php

namespace ConferenceTools\AttendanceTest\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Merchandise\ReadModel\Merchandise;
use ConferenceTools\Attendance\Domain\Purchasing\Basket;
use ConferenceTools\Attendance\Domain\Purchasing\BasketFactory;
use ConferenceTools\Attendance\Domain\Purchasing\MerchandiseQuantity;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Test\Unit\InMemoryRepository;

class BasketFactoryTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;
    /** @var Event[] */
    private $events;
    private $eventsRepository;
    /** @var Merchandise[] */
    private $merchandise;
    private $merchandiseRepository;
    /** @var Ticket[] */
    private $tickets;
    private $ticketsRepository;

    /** @dataProvider provideInvalidBaskets */
    public function testCreateInvalidBasket($tickets, $merchandise, \Throwable $expectedException)
    {
        $this->createTickets();
        $this->createMerchandise();
        $this->createEvents();

        $sut = new BasketFactory($this->ticketsRepository, $this->merchandiseRepository, $this->eventsRepository);

        $callback = function () use ($sut, $tickets, $merchandise) {
            $sut->createBasket($tickets, $merchandise);
        };

        $this->tester->expectThrowable($expectedException, $callback);
    }

    public function provideInvalidBaskets()
    {
        $ticketsSoldOut = new \DomainException('One or more of the tickets you selected has sold out or you have selected more than the quantity remaining');
        $merchandiseSoldOut = new \DomainException('One or more of the merchandise you selected has sold out or you have selected more than the quantity remaining');
        return [
            'Ticket doesnt exist' => [['404' => 7], [], $ticketsSoldOut],
            'Ticket over capacity' => [['0' => 17], [], $ticketsSoldOut],
            'Ticket not on sale'  => [['1' => 1], [], $ticketsSoldOut],
            'Event over capacity'  => [['2' => 10], [], new \DomainException('The tickets you have selected would put the event over capacity, please reduce the number of tickets you have selected')],
            'Merchandise doesnt exist' => [[], ['m_404' => 7], $merchandiseSoldOut],
            'Merchandise over capacity' => [[], ['m_0' => 17], $merchandiseSoldOut],
            'Merchandise not on sale' => [[], ['m_1' => 7], $merchandiseSoldOut],
            'Merchandise requires a ticket' => [[], ['m_0' => 1], new \DomainException('Please select at least one ticket to purchase')],
        ];
    }

    /** @dataProvider provideValidBaskets */
    public function testCreateValidBasket($tickets, $merchandise, Basket $expectedBasket)
    {
        $this->createTickets();
        $this->createMerchandise();
        $this->createEvents();

        $sut = new BasketFactory($this->ticketsRepository, $this->merchandiseRepository, $this->eventsRepository);

        $basket = $sut->createBasket($tickets, $merchandise);
        $this->tester->assertEquals($expectedBasket, $basket);
    }

    public function provideValidBaskets()
    {
        $ticketQuantity = new TicketQuantity('0', 3, Price::fromNetCost(100, 20));
        $merchandiseQuantity = new MerchandiseQuantity('m_0', 2, Price::fromNetCost(100, 20));
        $basket1 = new Basket([$ticketQuantity], []);
        $basket2 = new Basket([$ticketQuantity], [$merchandiseQuantity]);
        $basket3 = new Basket([], [new MerchandiseQuantity('m_2', 1, Price::fromNetCost(100, 20))]);

        return [
            [['0' => 3], [], $basket1],
            [['0' => 3], ['m_0' => 2], $basket2],
            [[], ['m_2' => 1], $basket3],
        ];
    }

    private function createEvents()
    {
        $this->eventsRepository = new InMemoryRepository();
        $this->events[0] = new Event('ev_0', new Descriptor('Event 1', ''), 100, new \DateTime(), new \DateTime());
        $this->events[1] = new Event('ev_1', new Descriptor('Event 2', ''), 1, new \DateTime(), new \DateTime());

        foreach ($this->events as $event) {
            $this->eventsRepository->add($event);
        }
    }

    private function createMerchandise()
    {
        $this->merchandiseRepository = new InMemoryRepository();
        $this->merchandise[0] = new Merchandise('m_0', new Descriptor('Merchandise 1', ''), 10, Price::fromNetCost(100, 20), true);
        $this->merchandise[0]->onSale();
        $this->merchandise[1] = new Merchandise('m_1', new Descriptor('Merchandise 2', ''), 10, Price::fromNetCost(100, 20), true);
        $this->merchandise[2] = new Merchandise('m_2', new Descriptor('Merchandise 3', ''), 10, Price::fromNetCost(100, 20), false);
        $this->merchandise[2]->onSale();

        foreach ($this->merchandise as $event) {
            $this->merchandiseRepository->add($event);
        }
    }

    private function createTickets()
    {
        $this->ticketsRepository = new InMemoryRepository();
        $this->tickets[0] = new Ticket('0', 'ev_0', new Descriptor('Ticket 1', ''), 10, Price::fromNetCost(100, 20));
        $this->tickets[0]->onSale();
        $this->tickets[1] = new Ticket('1', 'ev_0', new Descriptor('Ticket 2', ''), 10, Price::fromNetCost(100, 20));
        $this->tickets[2] = new Ticket('2', 'ev_1', new Descriptor('Ticket 3', ''), 10, Price::fromNetCost(100, 20));
        $this->tickets[2]->onSale();

        foreach ($this->tickets as $ticket) {
            $this->ticketsRepository->add($ticket);
        }
    }
}
