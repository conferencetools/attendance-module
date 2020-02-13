<?php

namespace ConferenceTools\Attendance\Domain\DataSharing;

use ConferenceTools\Attendance\Domain\DataSharing\Command\SendDelegateNotifications as SendDelegateNotificationsCommand;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Messaging\Domain\Email\Command\SendEmailInBackground;
use Doctrine\Common\Collections\Criteria;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\Message\MessageFirer;
use Phactor\ReadModel\Repository;

class NotifiesDelegates implements Handler
{
    private $delegateListRepository;
    private $delegateRepository;
    private $messageBus;

    public function __construct(Repository $delegateListRepository, Repository $delegateRepository, MessageFirer $messageBus)
    {
        $this->delegateListRepository = $delegateListRepository;
        $this->delegateRepository = $delegateRepository;
        $this->messageBus = $messageBus;
    }

    public function handle(DomainMessage $message)
    {
        if (!($message->getMessage() instanceof SendDelegateNotificationsCommand)) {
            return;
        }

        $delegatesOnAList = [];

        //At this point, they should all be terminated but we'll double check after loading
        /** @var \ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList[] $delegateLists */
        $delegateLists = $this->delegateListRepository->matching(Criteria::create());
        foreach ($delegateLists as $delegateList) {
            if (!$delegateList->isListTerminated()) {
                continue;
            }

            foreach ($delegateList->getDelegates() as $delegate) {
                if ($delegate->includeOnList()) {
                    $delegatesOnAList[$delegate->getDelegateId()][] = $delegate;
                }
            }
        }

        /** @var \ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate[] $delegates */
        $delegates = $this->delegateRepository->matching(Criteria::create()->where(Criteria::expr()->in('id', array_keys($delegatesOnAList))));

        foreach ($delegates as $delegate) {
            $this->messageBus->fire(new SendEmailInBackground($delegate->getEmail(), 'delegate-data-notification', [], [Delegate::class => $delegate->getId()]));
        }
    }
}