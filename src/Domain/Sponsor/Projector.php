<?php

namespace ConferenceTools\Attendance\Domain\Sponsor;

use ConferenceTools\Attendance\Domain\DataSharing\Event\DelegateListCreated;
use ConferenceTools\Attendance\Domain\Sponsor\ReadModel\Sponsor;
use Phactor\Identity\Generator;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;

class Projector implements Handler
{
    private $repository;
    private $generator;

    public function __construct(Repository $repository, Generator $generator)
    {
        $this->repository = $repository;
        $this->generator = $generator;
    }

    public function handle(DomainMessage $message)
    {
        $message = $message->getMessage();
        switch (true) {
            case ($message instanceof Command\CreateSponsor):
                $this->createSponsor($message);
                break;
            case $message instanceof Command\AddQuestion:
                $this->addQuestion($message);
                break;
            case $message instanceof Command\DeleteQuestion:
                $this->deleteQuestion($message);
                break;
            case $message instanceof DelegateListCreated:
                $this->delegateListCreated($message);
        }

        $this->repository->commit();
    }

    private function addQuestion(Command\AddQuestion $message): void
    {
        $sponsor = $this->fetchSponsor($message->getSponsorId());
        $sponsor->addQuestion($message->getQuestion());
    }

    private function createSponsor(Command\CreateSponsor $message): void
    {
        $this->repository->add(new Sponsor($this->generator->generateIdentity(), $message->getName(), $message->getUser()));
    }

    private function deleteQuestion(Command\DeleteQuestion $message): void
    {
        $sponsor = $this->fetchSponsor($message->getSponsorId());
        $sponsor->deleteQuestion($message->getHandle());
    }

    private function fetchSponsor(string $sponsorId): Sponsor
    {
        $sponsor = $this->repository->get($sponsorId);
        return $sponsor;
    }

    private function delegateListCreated(DelegateListCreated $message): void
    {
        $sponsor = $this->repository->get($message->getOwner());
        if (!($sponsor instanceof Sponsor)) {
            return;
        }

        $sponsor->delegateListCreated($message->getId());
    }
}
