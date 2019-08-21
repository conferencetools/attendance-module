<?php

namespace ConferenceTools\Attendance\Domain\Merchandise;

use ConferenceTools\Attendance\Domain\Merchandise\Event\MerchandiseCreated;
use ConferenceTools\Attendance\Domain\Merchandise\Event\MerchandiseOnSale;
use ConferenceTools\Attendance\Domain\Merchandise\Event\MerchandiseWithdrawnFromSale;
use ConferenceTools\Attendance\Domain\Merchandise\Event\SaleDateScheduled;
use ConferenceTools\Attendance\Domain\Merchandise\Event\WithdrawDateScheduled;
use ConferenceTools\Attendance\Domain\Merchandise\ReadModel\Merchandise as MerchandiseReadModel;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;

class MerchandiseProjector implements Handler
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
            case $event instanceof MerchandiseCreated:
                $this->newMerchandise($event);
                break;
            case $event instanceof MerchandiseWithdrawnFromSale:
                $this->withdrawn($event);
                break;
            case $event instanceof MerchandiseOnSale:
                $this->onSale($event);
                break;
            case $event instanceof MerchandiseReserved:
                $this->merchandiseReserved($event);
                break;
            case $event instanceof MerchandiseReservationExpired:
                $this->merchandiseExpired($event);
                break;
            case $event instanceof SaleDateScheduled:
                $this->saleDateScheduled($event);
                break;
            case $event instanceof WithdrawDateScheduled:
                $this->withdrawDateScheduled($event);

        }

        $this->repository->commit();
    }

    private function newMerchandise(MerchandiseCreated $event)
    {
        $entity = new MerchandiseReadModel($event->getId(), $event->getDescriptor(), $event->getQuantity(), $event->getPrice(), $event->getRequiresTicket());
        $this->repository->add($entity);
    }

    private function withdrawn(MerchandiseWithdrawnFromSale $event)
    {
        /** @var MerchandiseReadModel $merchandise */
        $merchandise = $this->repository->get($event->getId());
        $merchandise->withdraw();
    }

    private function onSale(MerchandiseOnSale $event)
    {
        /** @var MerchandiseReadModel $merchandise */
        $merchandise = $this->repository->get($event->getId());
        $merchandise->onSale();
    }

    private function merchandiseReserved(MerchandiseReserved $message)
    {
        /** @var MerchandiseReadModel $entity */
        $entity = $this->repository->get($message->getMerchandiseId());
        $entity->decreaseRemainingBy($message->getQuantity());
    }

    private function merchandiseExpired(MerchandiseReservationExpired $message)
    {
        /** @var MerchandiseReadModel $entity */
        $entity = $this->repository->get($message->getMerchandiseId());
        if ($entity !== null) {
            $entity->increaseRemainingBy($message->getQuantity());
        }
    }

    private function saleDateScheduled(SaleDateScheduled $message)
    {
        /** @var MerchandiseReadModel $entity */
        $entity = $this->repository->get($message->getId());
        if ($entity !== null) {
            $entity->onSaleFrom($message->getWhen());
        }
    }

    private function withdrawDateScheduled(WithdrawDateScheduled $message)
    {
        /** @var MerchandiseReadModel $entity */
        $entity = $this->repository->get($message->getId());
        if ($entity !== null) {
            $entity->withdrawFrom($message->getWhen());
        }
    }
}