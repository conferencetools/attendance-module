<?php

namespace ConferenceTools\AttendanceTest\Domain\Discounting;

use ConferenceTools\Attendance\Domain\Discounting\Command\AddCode;
use ConferenceTools\Attendance\Domain\Discounting\Command\CheckDiscountAvailability;
use ConferenceTools\Attendance\Domain\Discounting\Command\CreateDiscount;
use ConferenceTools\Attendance\Domain\Discounting\Discount;
use ConferenceTools\Attendance\Domain\Discounting\DiscountType;
use ConferenceTools\Attendance\Domain\Discounting\Event\CodeAdded;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountAvailable;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountCreated;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountWithdrawn;
use ConferenceTools\Attendance\Domain\Ticketing\AvailabilityDates;
use Phactor\Test\ActorHelper;

/**
 * @covers \ConferenceTools\Attendance\Domain\Discounting\DiscountType
 */
class DiscountTypeTest extends \Codeception\Test\Unit
{
    /** @var ActorHelper */
    private $helper;
    private $actorId = '';

    public function _before()
    {
        $this->helper = new ActorHelper(DiscountType::class);
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testCreateDiscountWithoutDates()
    {
        $availabilityDates = AvailabilityDates::always();
        $this->helper->when($this->createDiscountCommand($availabilityDates));
        $this->helper->expect($this->discountCreatedEvent($availabilityDates));
        $this->helper->expectNoMoreMessages();
    }

    public function testCreateDiscountAvailableLater()
    {
        $availabilityDates = AvailabilityDates::after((new \DateTime())->add(new \DateInterval('P1D')));
        $this->helper->when($this->createDiscountCommand($availabilityDates));
        $this->helper->expect($this->discountCreatedEvent($availabilityDates));
        $this->helper->expect(new CheckDiscountAvailability($this->actorId, $availabilityDates));
    }

    public function testCreateDiscountAvailableLaterWithExpiry()
    {
        $availabilityDates = AvailabilityDates::between(
            (new \DateTime())->add(new \DateInterval('P1D')),
            (new \DateTime())->add(new \DateInterval('P2D'))
        );
        $this->helper->when($this->createDiscountCommand($availabilityDates));
        $this->helper->expect($this->discountCreatedEvent($availabilityDates));
        $this->helper->expect(new CheckDiscountAvailability($this->actorId, $availabilityDates));
    }

    public function testCreateDiscountWithExpiry()
    {

        $availabilityDates = AvailabilityDates::until((new \DateTime())->add(new \DateInterval('P1D')));
        $this->helper->when($this->createDiscountCommand($availabilityDates));
        $this->helper->expect($this->discountCreatedEvent($availabilityDates));
        $this->helper->expect(new CheckDiscountAvailability($this->actorId, $availabilityDates));
    }

    public function testAddDiscountCode()
    {
        $availabilityDates = AvailabilityDates::always();
        $this->helper->given($this->discountTypeHasBeenCreated($availabilityDates));
        $this->helper->when(new AddCode($this->actorId, 'discountCode'));
        $this->helper->expect(new CodeAdded($this->actorId, 'discountCode'));
    }

    public function testDiscountMakesItselfAvailable()
    {
        $availabilityDates = AvailabilityDates::between(
            (new \DateTime())->add(new \DateInterval('P1D')),
            (new \DateTime())->add(new \DateInterval('P2D'))
        );
        $this->helper->given($this->discountTypeHasBeenCreated($availabilityDates));
        $this->helper->when(new CheckDiscountAvailability($this->actorId, $availabilityDates));
        $this->helper->expect(new DiscountAvailable($this->actorId));
        $this->helper->expect(new CheckDiscountAvailability($this->actorId, $availabilityDates));
    }

    public function testDiscountWithdrawsItself()
    {
        $availabilityDates = AvailabilityDates::until(
            (new \DateTime())->add(new \DateInterval('P1D'))
        );
        $this->helper->given($this->discountTypeHasBeenCreated($availabilityDates));
        $this->helper->when(new CheckDiscountAvailability($this->actorId, $availabilityDates));
        $this->helper->expect(new DiscountWithdrawn($this->actorId));
        $this->helper->expectNoMoreMessages();
    }

    private function createDiscountCommand(AvailabilityDates $availabilityDates): CreateDiscount
    {
        return new CreateDiscount('Huge discount', $availabilityDates, Discount::percentage(25));
    }

    private function discountCreatedEvent(AvailabilityDates $availabilityDates): DiscountCreated
    {
        return new DiscountCreated(
            $this->actorId,
            'Huge discount',
            Discount::percentage(25),
            $availabilityDates,
            $availabilityDates->availableNow()
        );
    }

    private function discountTypeHasBeenCreated(AvailabilityDates $availabilityDates): array
    {
        return [
            $this->createDiscountCommand($availabilityDates),
            $this->discountCreatedEvent($availabilityDates),
        ];
    }
}
