<?php

namespace ConferenceTools\Attendance\Domain\Delegate;


use ConferenceTools\Attendance\Domain\Delegate\Command\CheckIn;
use ConferenceTools\Attendance\Domain\Delegate\Command\UpdateDelegateDetails;
use ConferenceTools\Attendance\Domain\Delegate\Event\CheckedIn;
use ConferenceTools\Attendance\Domain\Delegate\Event\CheckinIdGenerated;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateDetailsUpdated;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseCompleted;
use ConferenceTools\Attendance\Domain\Purchasing\Purchase;
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
    /**
     *
     */
    private $purchaseCompleted;
    /**
     *
     */
    private $checkinId;

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

        $this->subscribe(Purchase::class, $command->getPurchaseId());
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

        $checkinId = $this->generateCheckinId();
        if ($this->email !== $command->getEmail() && $this->purchaseCompleted) {
            $this->fire(new CheckinIdGenerated($this->id(), $this->email, $checkinId));
        }
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

    protected function handlePurchaseCompleted(PurchaseCompleted $event)
    {
        //generate ticket
        //@TODO make this fully random by exposing the identity generator from the base class.
        $checkinId = $this->generateCheckinId();
        $this->fire(new CheckinIdGenerated($this->id(), $this->email, $checkinId));
    }

    protected function applyCheckinIdGenerated(CheckinIdGenerated $event)
    {
        $this->checkinId = $event->getCheckinId();
    }

    protected function applyPurchaseCompleted(PurchaseCompleted $event)
    {
        $this->purchaseCompleted = true;
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

    /**
     * @return string
     */
    private function generateCheckinId(): string
    {
        return $this->generateIdentity();
    }
}