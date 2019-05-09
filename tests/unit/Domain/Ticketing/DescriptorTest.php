<?php
namespace ConferenceTools\AttendanceTest\Domain\Ticketing;

use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;

class DescriptorTest extends \Codeception\Test\Unit
{
    /** @var \UnitTester */
    protected $tester;

    public function testCreate()
    {
        $sut = new Descriptor('name', 'description');
        $this->tester->assertEquals('name', $sut->getName());
        $this->tester->assertEquals('description', $sut->getDescription());
    }
}