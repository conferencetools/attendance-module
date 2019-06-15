<?php

namespace ConferenceTools\AttendanceTest\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Ticketing\Price;

class PriceTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testFromNetCost()
    {
        $sut = Price::fromNetCost(200, 20);
        $this->tester->assertEquals(200, $sut->getNet());
        $this->tester->assertEquals(240, $sut->getGross());
        $this->tester->assertEquals(20, $sut->getTaxRate());
        $this->tester->assertEquals(40, $sut->getTax());
    }

    public function testFromGrossCost()
    {
        $sut = Price::fromGrossCost(240, 20);
        $this->tester->assertEquals(200, $sut->getNet());
        $this->tester->assertEquals(240, $sut->getGross());
        $this->tester->assertEquals(20, $sut->getTaxRate());
        $this->tester->assertEquals(40, $sut->getTax());
    }

    public function testCompare()
    {
        $big = Price::fromNetCost(100, 20);
        $mid = Price::fromNetCost(50, 20);
        $sml = Price::fromNetCost(10, 20);

        $this->tester->assertEquals(0, $mid->compare($mid));
        $this->tester->assertEquals(1, $mid->compare($sml));
        $this->tester->assertEquals(-1, $mid->compare($big));
    }

    public function testLessThan()
    {
        $big = Price::fromNetCost(100, 20);
        $sml = Price::fromNetCost(50, 20);

        $this->tester->assertTrue($sml->lessThan($big));
        $this->tester->assertFalse($big->lessThan($sml));
        $this->tester->assertFalse($big->lessThan($big));
    }

    public function testGreaterThan()
    {
        $big = Price::fromNetCost(100, 20);
        $sml = Price::fromNetCost(50, 20);

        $this->tester->assertFalse($sml->greaterThan($big));
        $this->tester->assertTrue($big->greaterThan($sml));
        $this->tester->assertFalse($big->greaterThan($big));
    }

    public function testEquals()
    {
        $a = Price::fromNetCost(100, 20);
        $b = Price::fromNetCost(100, 20);
        $c = Price::fromNetCost(10, 20);

        $this->tester->assertTrue($a->equals($b));
        $this->tester->assertFalse($a->equals($c));
    }

    public function testAdd()
    {
        $sut = Price::fromNetCost(20, 20);

        $this->tester->assertEquals(40, $sut->add($sut)->getNet());
    }

    public function testMultiply()
    {
        $sut = Price::fromNetCost(100, 20);
        $this->tester->assertEquals(200, $sut->multiply(2)->getNet());
    }

    public function testSubtract()
    {
        $sut = Price::fromNetCost(20, 20);

        $this->tester->assertEquals(0, $sut->subtract($sut)->getNet());
    }

    public function testIsSameTaxRate()
    {
        $a = Price::fromNetCost(20, 20);
        $b = Price::fromNetCost(20, 20);
        $c = Price::fromNetCost(20, 15);

        $this->assertTrue($a->isSameTaxRate($b));
        $this->assertFalse($a->isSameTaxRate($c));
    }
}
