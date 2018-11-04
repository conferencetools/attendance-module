<?php

namespace ConferenceTools\Attendance\Domain\Delegate;


use Phactor\Actor\AbstractActor;
use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketAllocatedToDelegate;

class Delegate extends AbstractActor
{
    private $firstname;
    private $lastname;
    private $email;
    private $company;
    private $twitter;
    private $requirements;
    private $purchaseId;
    private $tickets;

    protected function handleRegisterDelegate(RegisterDelegate $command)
    {
        $this->fire(new DelegateRegistered(
            $this->id(),
            $command->getPurchaseId(),
            $command->getFirstname(),
            $command->getLastname(),
            $command->getEmail(),
            $command->getCompany(),
            $command->getTwitter(),
            $command->getRequirements()
        ));
    }

    protected function applyDelegateRegistered(DelegateRegistered $event)
    {
        $this->purchaseId = $event->getPurchaseId();
        $this->firstname = $event->getFirstname();
        $this->lastname = $event->getLastname();
        $this->email = $event->getEmail();
        $this->company = $event->getCompany();
        $this->twitter = $event->getTwitter();
        $this->requirements = $event->getRequirements();
    }

    protected function applyTicketAllocatedToDelegate(TicketAllocatedToDelegate $event)
    {
        $this->tickets[] = $event->getTicketId();
    }
}