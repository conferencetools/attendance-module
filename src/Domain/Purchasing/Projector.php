<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;

use Carnage\Phactor\Message\DomainMessage;
use Carnage\Phactor\Message\Handler;
use Carnage\Phactor\ReadModel\Repository;
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
            case $event instanceof TicketsReserved:
                $this->ticketsReserved($event);
                break;
        }

        $this->repository->commit();
    }

    private function ticketsReserved(TicketsReserved $event)
    {
        $entity = $this->repository->get($event->getId());
        if (!($entity instanceof Purchase)) {
            $entity = new Purchase($event->getId());
            $this->repository->add($entity);
        }

        $entity->addTickets($event->getTicketId(), $event->getQuantity());
    }
}