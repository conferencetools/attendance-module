<?php


namespace ConferenceTools\Attendance\Domain\Delegate;


use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateDetailsUpdated;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketAllocatedToDelegate;

class Projector implements Handler
{
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(DomainMessage $message)
    {
        $event = $message->getMessage();
        switch (true) {
            case $event instanceof DelegateRegistered:
                $this->delegateRegistered($event);
                break;
            case $event instanceof TicketAllocatedToDelegate:
                $this->ticketAllocated($event);
                break;
            case $event instanceof DelegateDetailsUpdated:
                $this->updateDetails($event);
                break;
        }

        $this->repository->commit();
    }

    private function delegateRegistered(DelegateRegistered $event): void
    {
        $delegate = new ReadModel\Delegate(
            $event->getId(),
            $event->getPurchaseId(),
            $event->getName(),
            $event->getEmail(),
            $event->getCompany(),
            $event->getDietaryRequirements(),
            $event->getRequirements()
        );

        $this->repository->add($delegate);
    }

    private function ticketAllocated(TicketAllocatedToDelegate $event): void
    {
        $delegate = $this->repository->get($event->getDelegateId());
        $delegate->addTicket($event->getTicketId());
    }

    private function updateDetails(DelegateDetailsUpdated $event)
    {
        $delegate = $this->repository->get($event->getDelegateId());
        $delegate->updateDetails(
            $event->getName(),
            $event->getEmail(),
            $event->getCompany(),
            $event->getDietaryRequirements(),
            $event->getRequirements()
        );
    }
}