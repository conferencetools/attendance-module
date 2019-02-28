<?php

namespace ConferenceTools\Attendance\Domain\Delegate;


use ConferenceTools\Attendance\Domain\Delegate\Command\CheckIn;
use ConferenceTools\Attendance\Domain\Delegate\Command\UpdateDelegateDetails;
use ConferenceTools\Attendance\Domain\Delegate\Event\CheckedIn;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateDetailsUpdated;
use Phactor\Actor\AbstractActor;
use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketAllocatedToDelegate;

class Delegate extends AbstractActor
{
    private $email;
    private $company;
    private $requirements;
    private $purchaseId;
    private $tickets;
    private $dietaryRequirements;
    private $name;
    private $delegateType;
    private $checkedIn = false;

    protected function handleRegisterDelegate(RegisterDelegate $command)
    {
        $this->fire(new DelegateRegistered(
            $this->id(),
            $command->getPurchaseId(),
            $command->getName(),
            $command->getEmail(),
            $command->getCompany(),
            $command->getDietaryRequirements(),
            $command->getRequirements(),
            $command->getDelegateType()
        ));
    }

    protected function applyDelegateRegistered(DelegateRegistered $event)
    {
        $this->purchaseId = $event->getPurchaseId();
        $this->name = $event->getName();
        $this->dietaryRequirements = $event->getDietaryRequirements();
        $this->email = $event->getEmail();
        $this->company = $event->getCompany();
        $this->requirements = $event->getRequirements();
        $this->delegateType = $event->getDelegateType();
    }

    protected function handleUpdateDelegateDetails(UpdateDelegateDetails $command)
    {
        $this->fire(new DelegateDetailsUpdated(
            $this->id(),
            $command->getName(),
            $command->getEmail(),
            $command->getCompany(),
            $command->getDietaryRequirements(),
            $command->getRequirements()
        ));
    }

    protected function applyDelegateDetailsUpdated(DelegateDetailsUpdated $event)
    {
        $this->name = $event->getName();
        $this->dietaryRequirements = $event->getDietaryRequirements();
        $this->email = $event->getEmail();
        $this->company = $event->getCompany();
        $this->requirements = $event->getRequirements();
    }

    protected function applyTicketAllocatedToDelegate(TicketAllocatedToDelegate $event)
    {
        $this->tickets[] = $event->getTicketId();
    }

    protected function handleCheckIn(CheckIn $command)
    {
        if (!$this->checkedIn) {
            $this->fire(new CheckedIn($this->id()));
        }
    }

    protected function applyCheckedIn(CheckedIn $event)
    {
        $this->checkedIn = true;
    }
}