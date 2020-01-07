<?php

namespace ConferenceTools\Attendance\Domain\DataSharing;

use ConferenceTools\Attendance\Domain\DataSharing\Command\AddDelegate;
use ConferenceTools\Attendance\Domain\DataSharing\Command\CreateDelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\Command\MakeListAvailable;
use ConferenceTools\Attendance\Domain\DataSharing\Command\SetLastCollectionTime;
use ConferenceTools\Attendance\Domain\DataSharing\Command\SetListAvailableTime;
use ConferenceTools\Attendance\Domain\DataSharing\Command\TerminateCollection;
use ConferenceTools\Attendance\Domain\DataSharing\Event\CollectionTerminated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateAdded;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateListCreated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateUpdated;
use ConferenceTools\Attendance\Domain\DataSharing\Event\LastCollectionTimeSet;
use ConferenceTools\Attendance\Domain\DataSharing\Event\ListAvailable;
use ConferenceTools\Attendance\Domain\DataSharing\Event\ListAvailableTimeSet;
use Phactor\Actor\AbstractActor;

class DelegateList extends AbstractActor
{
    private $owner;
    private $optIns;
    private $lastCollectionTime;
    private $listAvailableTime;
    private $delegates;

    protected function handleCreateDelegateList(CreateDelegateList $command)
    {
        $this->fire(new DelegateListCreated($this->id(), $command->getOwner(), ...$command->getOptIns()));
    }

    protected function applyDelegateListCreated(DelegateListCreated $event)
    {
        $this->owner = $event->getOwner();
        $this->optIns = $event->getOptIns();
    }

    protected function handleSetLastCollectionTime(SetLastCollectionTime $command)
    {
        $this->fire(new LastCollectionTimeSet($this->id(), $command->getLastCollectionTime()));
        $this->schedule(new TerminateCollection($this->id(), $command->getLastCollectionTime()), $command->getLastCollectionTime());
    }

    protected function applyLastCollectionTimeSet(LastCollectionTimeSet $event)
    {
        $this->lastCollectionTime = $event->getLastCollectionTime();
    }

    protected function handleTerminateCollection(TerminateCollection $command)
    {
        if ($this->lastCollectionTime == $command->getLastCollectionTime()) {
            $this->fire(new CollectionTerminated($this->id()));
        }
    }

    protected function handleSetListAvailableTime(SetListAvailableTime $command)
    {
        $this->fire(new ListAvailableTimeSet($this->id(), $command->getListAvailableTime()));
        $this->schedule(new MakeListAvailable($this->id(), $command->getListAvailableTime()), $command->getListAvailableTime());
    }

    protected function applyListAvailableTimeSet(ListAvailableTimeSet $event)
    {
        $this->listAvailableTime = $event->getListAvailableTime();
    }

    protected function handleMakeListAvailable(MakeListAvailable $command)
    {
        if ($this->listAvailableTime == $command->getListAvailableTime()) {
            $this->fire(new ListAvailable($this->id()));
        }
    }

    protected function handleAddDelegate(AddDelegate $command)
    {
        if (isset($this->delegates[$command->getDelegateId()])) {
            $this->fire(new DelegateUpdated($this->id(), $command->getDelegateId(), ...$command->getOptInConsents()));
        } else {
            $this->fire(new DelegateAdded($this->id(), $command->getDelegateId(), ...$command->getOptInConsents()));
        }
    }

    protected function applyDelegateAdded(DelegateAdded $event)
    {
        $this->delegates[$event->getDelegateId()] = $event->getOptInConsents();
    }


    protected function applyDelegateUpdated(DelegateUpdated $event)
    {
        $this->delegates[$event->getDelegateId()] = $event->getOptInConsents();
    }
}
