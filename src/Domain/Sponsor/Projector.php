<?php

namespace ConferenceTools\Attendance\Domain\Sponsor;

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
        if ($message instanceof Command\CreateSponsor) {
            $this->repository->add(new Sponsor($this->generator->generateIdentity(), $message->getName(), $message->getUser()));
        }

        $this->repository->commit();
    }
}
