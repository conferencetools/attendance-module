<?php

namespace ConferenceTools\Attendance\Domain\Reporting;

use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateDetailsUpdated;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;
use ConferenceTools\Attendance\Domain\Reporting\ReadModel\DelegateCatering;
use Doctrine\Common\Collections\Criteria;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;

class CateringReport implements Handler
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
            case $event instanceof PaymentMade:
                $this->paymentMade($event);
                break;
            case $event instanceof DelegateDetailsUpdated:
                $this->detailsUpdated($event);
                break;
        }

        $this->repository->commit();
    }

    private function delegateRegistered(DelegateRegistered $event): void
    {
        $report = new DelegateCatering($event->getId(), $event->getPurchaseId(), $event->getName(), $event->getDietaryRequirements());

        $this->repository->add($report);
    }

    private function paymentMade(PaymentMade $event): void
    {
        $delegate = $this->repository->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $event->getActorId())))->first();
        $delegate->purchasePaid();
    }

    private function detailsUpdated(DelegateDetailsUpdated $event): void
    {
        /** @var DelegateCatering $delegate */
        $delegate = $this->repository->get($event->getDelegateId());
        $delegate->detailsUpdated($event->getName(), $event->getDietaryRequirements());
    }
}