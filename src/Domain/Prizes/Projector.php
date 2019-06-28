<?php


namespace ConferenceTools\Attendance\Domain\Prizes;


use ConferenceTools\Attendance\Domain\Prizes\Event\PrizeGiveaway;
use ConferenceTools\Attendance\Domain\Prizes\Event\WinnerChosen;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
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
            case $event instanceof PrizeGiveaway:
                $this->prizeGiveaway($event);
                break;
            case $event instanceof WinnerChosen:
                $this->winnerChosen($event);
                break;
        }

        $this->repository->commit();
    }

    private function prizeGiveaway(PrizeGiveaway $event): void
    {
        $entity = new ReadModel\Prize($event->getId(), $event->getName());
        $this->repository->add($event);
    }

    private function winnerChosen(WinnerChosen $event): void
    {
        /** @var ReadModel\Prize $entity */
        $entity = $this->repository->get($event->getId());
        $entity->winnerChosen($event->getWinner());
    }
}