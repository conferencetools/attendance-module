<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing\ReadModel;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;

class TicketTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testCreate()
    {
        $ticketId = 'ticketId';
        $eventId = 'eventId';
        $descriptor = new Descriptor('name', 'description');
        $quantity = 10;
        $price = Price::fromNetCost(100, 20);
        $sut = new Ticket($ticketId, $eventId, $descriptor, $quantity, $price);

        $this->tester->assertEquals($ticketId, $sut->getId());
        $this->tester->assertEquals($eventId, $sut->getEventId());
        $this->tester->assertEquals($descriptor, $sut->getDescriptor());
        $this->tester->assertEquals($quantity, $sut->getQuantity());
        $this->tester->assertEquals($price, $sut->getPrice());
        $this->tester->assertEquals(false, $sut->isOnSale());
    }

    public function testOnSale()
    {
        $sut = $this->newTicket();
        $sut->onSale();
        $this->tester->assertEquals(true, $sut->isOnSale());
    }

    public function testWithdraw()
    {
        $sut = $this->newTicket();
        $sut->onSale();
        $sut->withdraw();
        $this->tester->assertEquals(false, $sut->isOnSale());
    }

    public function testDecreaseRemaining()
    {
        $sut = $this->newTicket();
        $sut->decreaseRemainingBy(3);
        $this->tester->assertEquals(7, $sut->getRemaining());
    }

    public function testIncreaseRemaining()
    {
        $sut = $this->newTicket();
        $sut->increaseRemainingBy(3);
        $this->tester->assertEquals(13, $sut->getRemaining());
    }

    private function newTicket(): Ticket
    {
        $ticketId = 'ticketId';
        $eventId = 'eventId';
        $descriptor = new Descriptor('name', 'description');
        $quantity = 10;
        $price = Price::fromNetCost(100, 20);
        return new Ticket($ticketId, $eventId, $descriptor, $quantity, $price);
    }
}
