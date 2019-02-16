<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;
use ConferenceTools\Attendance\Domain\Purchasing\Event\DiscountApplied;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketReservationExpired;
use ConferenceTools\Attendance\Domain\Ticketing\Event\TicketsReleased;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;
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
                break;
            case $event instanceof PaymentMade:
                $this->purchasePaid($event);
                break;
            case $event instanceof TicketReservationExpired:
                $this->purchaseTimeout($event);
                break;
            case $event instanceof DiscountApplied:
                $this->discountApplied($event);
                break;
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
        $entity = new Purchase($event->getId(), $event->getEmail(), $event->getDelegates());
        $this->repository->add($entity);
    }

    private function updatePrice(OutstandingPaymentCalculated $event)
    {
        /** @var Purchase $entity */
        $entity = $this->repository->get($event->getPurchaseId());
        $entity->updateTotal($event->getTotal());
    }

    private function purchasePaid(PaymentMade $event)
    {
        /** @var Purchase $entity */
        $entity = $this->repository->get($event->getActorId());
        $entity->paid();
    }

    private function purchaseTimeout(TicketReservationExpired $event)
    {
        $entity = $this->repository->get($event->getId());
        if ($entity instanceof Purchase) {
            $this->repository->remove($entity);
        }
    }

    private function discountApplied(DiscountApplied $event)
    {
        /** @var Purchase $entity */
        $entity = $this->repository->get($event->getPurchaseId());
        $entity->discountApplied($event->getDiscountId(), $event->getDiscountCode());
    }
}