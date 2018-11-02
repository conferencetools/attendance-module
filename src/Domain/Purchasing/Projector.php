<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;

use Carnage\Phactor\Message\DomainMessage;
use Carnage\Phactor\Message\Handler;
use Carnage\Phactor\ReadModel\Repository;
use ConferenceTools\Attendance\Domain\Purchasing\Event\OutstandingPaymentCalculated;
use ConferenceTools\Attendance\Domain\Purchasing\Event\PurchaseStartedBy;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;

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
            case $event instanceof PurchaseStartedBy:
                $this->purchaseStartedBy($event);
                break;
            case $event instanceof TicketsReserved:
                $this->ticketsReserved($event);
                break;
            case $event instanceof OutstandingPaymentCalculated:
                $this->updatePrice($event);
        }

        $this->repository->commit();
    }

    private function ticketsReserved(TicketsReserved $event)
    {
        /** @var Purchase $entity */
        $entity = $this->repository->get($event->getId());
        $entity->addTickets($event->getTicketId(), $event->getQuantity());
    }

    private function purchaseStartedBy(PurchaseStartedBy $event)
    {
        $entity = new Purchase($event->getId(), $event->getEmail());
        $this->repository->add($entity);
    }

    private function updatePrice(OutstandingPaymentCalculated $event)
    {
        /** @var Purchase $entity */
        $entity = $this->repository->get($event->getPurchaseId());
        $entity->updateTotal($event->getTotal());
    }
}