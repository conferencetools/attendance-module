<?php

namespace ConferenceTools\Attendance\Domain\DataSharing;

use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList as DelegateListEntity;
use Phactor\ReadModel\Repository;

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
            case $event instanceof Event\DelegateListCreated:
                $this->newList($event);
                break;
            case $event instanceof Event\ListAvailableTimeSet:
                $this->setAvailableTime($event);
                break;
            case $event instanceof Event\LastCollectionTimeSet:
                $this->setLastCollectionTime($event);
                break;
            case $event instanceof Event\DelegateAdded:
                $this->addDelegate($event);
                break;
            case $event instanceof Event\DelegateUpdated:
                $this->updateDelegate($event);
                break;
            case $event instanceof Event\CollectionTerminated:
                $this->terminate($event);
                break;
            case $event instanceof Event\ListAvailable:
                $this->makeAvailable($event);
                break;
        }

        $this->repository->commit();
    }

    private function newList(Event\DelegateListCreated $event)
    {
        $entity = new DelegateListEntity($event->getId(), $event->getOwner(), ...$event->getOptIns());
        $this->repository->add($entity);
    }

    private function setLastCollectionTime(Event\LastCollectionTimeSet $event)
    {
        $entity = $this->fetchList($event->getId());
        $entity->setLastCollectionTime($event->getLastCollectionTime());
    }

    private function setAvailableTime(Event\ListAvailableTimeSet $event)
    {
        $entity = $this->fetchList($event->getId());
        $entity->setAvailableTime($event->getListAvailableTime());
    }

    private function fetchList(string $id): DelegateListEntity
    {
        return $this->repository->get($id);
    }

    private function terminate(Event\CollectionTerminated $event)
    {
        $list = $this->fetchList($event->getId());
        $list->terminate();
    }

    private function makeAvailable(Event\ListAvailable $event)
    {
        $list = $this->fetchList($event->getId());
        $list->makeAvailable();
    }

    private function addDelegate(Event\DelegateAdded $event)
    {
        $list = $this->fetchList($event->getId());
        $list->addDelegate($event->getDelegateId(), ...$event->getOptInConsents());
    }

    private function updateDelegate(Event\DelegateUpdated $event)
    {
        $list = $this->fetchList($event->getId());
        $list->updateDelegate($event->getDelegateId(), ...$event->getOptInConsents());
    }
}
