<?php


namespace ConferenceTools\Attendance\Domain\Discounting;


use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountCreated;
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
            case $event instanceof DiscountCreated:
                $this->discountCreated($event);
                break;
        }

        $this->repository->commit();
    }

    private function discountCreated(DiscountCreated $event): void
    {
        $model = new ReadModel\DiscountType($event->getId(), $event->getName(), $event->getDiscount());#
        $this->repository->add($model);
    }
}
