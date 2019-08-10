<?php


namespace ConferenceTools\Attendance\Domain\Delegate;


use ConferenceTools\Attendance\Domain\Delegate\Event\CheckedIn;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateDetailsUpdated;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use Doctrine\Common\Collections\Criteria;
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
            case $event instanceof PaymentMade:
                $this->paymentMade($event);
                break;
            case $event instanceof CheckedIn:
                $this->checkIn($event);
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
            $event->getRequirements(),
            $event->getDelegateType()
        );

        $this->repository->add($delegate);
    }

    private function ticketAllocated(TicketAllocatedToDelegate $event): void
    {
        $delegate = $this->fetchDelegate($event->getDelegateId());
        $delegate->addTicket($event->getTicketId());
    }

    private function updateDetails(DelegateDetailsUpdated $event)
    {
        $delegate = $this->fetchDelegate($event->getDelegateId());
        $delegate->updateDetails(
            $event->getName(),
            $event->getEmail(),
            $event->getCompany(),
            $event->getDietaryRequirements(),
            $event->getRequirements()
        );
    }

    private function paymentMade(PaymentMade $event): void
    {
        $delegates = $this->repository->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $event->getActorId())));
        foreach ($delegates as $delegate) {
            $delegate->purchasePaid();
        }
    }

    private function checkIn(CheckedIn $event)
    {
        $delegate = $this->fetchDelegate($event->getId());
        $delegate->checkIn();
    }

    private function fetchDelegate(string $delegateId): ReadModel\Delegate
    {
        $delegate = $this->repository->get($delegateId);
        return $delegate;
    }
}