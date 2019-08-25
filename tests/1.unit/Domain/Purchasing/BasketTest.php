<?php

namespace ConferenceTools\AttendanceTest\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Purchasing\Basket;
use ConferenceTools\Attendance\Domain\Purchasing\MerchandiseQuantity;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\Price;

class BasketTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testCreateWithInvalidTypes()
    {
        $this->tester->expectThrowable(\TypeError::class, function () {
            new Basket([new \stdClass()], [new \stdClass()]);
        });
    }

    public function testCreateWithEmptyBasket()
    {
        $this->tester->expectThrowable(\DomainException::class, function () {
            new Basket([], []);
        });
    }

    public function testCreate()
    {
        $tickets = [new TicketQuantity('tID', 1, Price::fromNetCost(100, 20))];
        $merchandise = [new MerchandiseQuantity('mID', 3, Price::fromNetCost(100, 20))];

        $sut = new Basket($tickets, $merchandise);
        $this->tester->assertEquals($tickets, $sut->getTickets());
        $this->tester->assertEquals($merchandise, $sut->getMerchandise());
    }

    /** @dataProvider provideGetTotal */
    public function testGetTotal($ticketQuantities, $merchandiseQuantities, $expected)
    {
        $sut = new Basket($ticketQuantities, $merchandiseQuantities);
        $this->tester->assertEquals($expected, $sut->getTotal());
    }

    public function provideGetTotal()
    {
        $ticketQuantity = new TicketQuantity('tID', 1, Price::fromNetCost(100, 20));
        $merchandiseQuantity = new MerchandiseQuantity('mID', 1, Price::fromNetCost(100, 20));
        return [
            [[$ticketQuantity], [$merchandiseQuantity], Price::fromNetCost(200, 20)],
            [[$ticketQuantity], [], Price::fromNetCost(100, 20)],
            [[], [$merchandiseQuantity], Price::fromNetCost(100, 20)],
        ];
    }
}
