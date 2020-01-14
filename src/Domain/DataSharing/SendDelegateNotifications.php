<?php

namespace ConferenceTools\Attendance\Domain\DataSharing;

use Phactor\Actor\AbstractActor;
use Phactor\Message\DomainMessage;

class SendDelegateNotifications extends AbstractActor
{
    private $numberOfLists = 0;

    protected function applyDelegateListCreated(Event\DelegateListCreated $event)
    {
        $this->numberOfLists++;
    }

    protected function handleCollectionTerminated(Event\CollectionTerminated $event)
    {
        if ($this->numberOfLists === 1) {
            $this->fire(new Command\SendDelegateNotifications());
        }
    }

    protected function applyCollectionTerminated(Event\CollectionTerminated $event)
    {
        $this->numberOfLists--;
    }

    public static function generateId(DomainMessage $message): ?string
    {
        return hash('sha256', self::class);
    }
}