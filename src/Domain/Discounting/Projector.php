<?php


namespace ConferenceTools\Attendance\Domain\Discounting;


use ConferenceTools\Attendance\Domain\Discounting\Event\CodeAdded;
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

    public function handle(DomainMessage $message): void
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
            case $event instanceof CodeAdded:
                $this->addCode($event);
                break;
        }

        $this->repository->commit();
    }

    private function discountCreated(DiscountCreated $event): void
    {
        $model = new ReadModel\DiscountType($event->getId(), $event->getName(), $event->getDiscount(), $event->isAvailableNow());#
        $this->repository->add($model);
    }

    private function withdrawn(DiscountWithdrawn $event): void
    {
        /** @var ReadModel\DiscountType $discount */
        $discount = $this->repository->get($event->getId());
        $discount->withdraw();
    }

    private function available(DiscountAvailable $event): void
    {
        /** @var ReadModel\DiscountType $discount */
        $discount = $this->repository->get($event->getId());
        $discount->available();
    }

    private function addCode(CodeAdded $event): void
    {
        /** @var ReadModel\DiscountType $discount */
        $discount = $this->repository->get($event->getId());
        $discount->addCode($event->getCode());
    }
}
