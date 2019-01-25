<?php


namespace ConferenceTools\Attendance\Domain\Discounting;


use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountAvailable;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountCreated;
use ConferenceTools\Attendance\Domain\Discounting\Event\DiscountWithdrawn;
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
            case $event instanceof DiscountWithdrawn:
                $this->withdrawn($event);
                break;
            case $event instanceof DiscountAvailable:
                $this->available($event);
                break;
        }

        $this->repository->commit();
    }

    private function discountCreated(DiscountCreated $event): void
    {
        $model = new ReadModel\DiscountType($event->getId(), $event->getName(), $event->getDiscount(), $event->isAvailableNow());#
        $this->repository->add($model);
    }

    private function withdrawn(DiscountWithdrawn $event)
    {
        /** @var ReadModel\DiscountType $discount */
        $discount = $this->repository->get($event->getId());
        $discount->withdraw();
    }

    private function available(DiscountAvailable $event)
    {
        /** @var ReadModel\DiscountType $discount */
        $discount = $this->repository->get($event->getId());
        $discount->available();
    }
}
