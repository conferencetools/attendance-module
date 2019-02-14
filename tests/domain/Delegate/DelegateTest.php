<?php

namespace ConferenceTools\AttendanceTest\Domain\Delegate;

use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\Command\UpdateDelegateDetails;
use ConferenceTools\Attendance\Domain\Delegate\Delegate;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateDetailsUpdated;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use Phactor\Test\ActorHelper;

/**
 * @covers \ConferenceTools\Attendance\Domain\Delegate\Delegate
 */
class DelegateTest extends \Codeception\Test\Unit
{
    /** @var ActorHelper */
    private $helper;
    private $actorId = '';

    public function _before()
    {
        $this->helper = new ActorHelper(Delegate::class);
        $this->actorId = $this->helper->getActorIdentity()->getId();
    }

    public function testRegisterDelegate()
    {
        $this->helper->when(new RegisterDelegate(
            'purchaseId',
            'name',
            'email@email.com',
            'company',
            new DietaryRequirements(DietaryRequirements::NONE, ''),
            '',
            'delegate'
        ));
        $this->helper->expect($this->delegateRegisteredEvent());
        $this->helper->expectNoMoreMessages();
    }

    public function testUpdateDelegateDetails()
    {
        $this->helper->given([$this->delegateRegisteredEvent()]);
        $this->helper->when(new UpdateDelegateDetails(
            $this->actorId,
            'new name',
            'newemail@email.com',
            'newcompany',
            new DietaryRequirements(DietaryRequirements::NONE, ''),
            ''
        ));
        $this->helper->expect(
            new DelegateDetailsUpdated(
                $this->actorId,
                'new name',
                'newemail@email.com',
                'newcompany',
                new DietaryRequirements(DietaryRequirements::NONE, ''),
                ''
            )
        );
        $this->helper->expectNoMoreMessages();
    }

    /**
     * @return DelegateRegistered
     */
    public function delegateRegisteredEvent(): DelegateRegistered
    {
        return new DelegateRegistered(
            $this->actorId,
            'purchaseId',
            'name',
            'email@email.com',
            'company',
            new DietaryRequirements(DietaryRequirements::NONE, ''),
            '',
            'delegate'
        );
    }
}