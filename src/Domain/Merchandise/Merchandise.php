<?php

namespace ConferenceTools\Attendance\Domain\Merchandise;

use ConferenceTools\Attendance\Domain\Merchandise\Command\CreateMerchandise;
use ConferenceTools\Attendance\Domain\Merchandise\Command\ScheduleSaleDate;
use ConferenceTools\Attendance\Domain\Merchandise\Command\ScheduleWithdrawDate;
use ConferenceTools\Attendance\Domain\Merchandise\Command\ShouldMerchandiseBePutOnSale;
use ConferenceTools\Attendance\Domain\Merchandise\Command\ShouldMerchandiseBeWithdrawn;
use ConferenceTools\Attendance\Domain\Merchandise\Event\MerchandiseCreated;
use ConferenceTools\Attendance\Domain\Merchandise\Event\MerchandiseOnSale;
use ConferenceTools\Attendance\Domain\Merchandise\Event\MerchandiseWithdrawnFromSale;
use ConferenceTools\Attendance\Domain\Merchandise\Event\SaleDateScheduled;
use ConferenceTools\Attendance\Domain\Merchandise\Event\WithdrawDateScheduled;
use Phactor\Actor\AbstractActor;

class Merchandise extends AbstractActor
{
    private $descriptor;
    private $quantity;
    private $onSale = false;
    private $price;
    private $requiresTicket;
    private $putOnSaleOn;
    private $withdrawOn;

    protected function handleCreateMerchandise(CreateMerchandise $command)
    {
        $this->fire(new MerchandiseCreated(
            $this->id(),
            $command->getDescriptor(),
            $command->getQuantity(),
            $command->getPrice(),
            $command->getRequiresTicket()
        ));
    }

    protected function applyMerchandiseCreated(MerchandiseCreated $event)
    {
        $this->descriptor = $event->getDescriptor();
        $this->quantity = $event->getQuantity();
        $this->price = $event->getPrice();
        $this->requiresTicket = $event->getRequiresTicket();
    }

    protected function handleScheduleSaleDate(ScheduleSaleDate $command)
    {
        if (!$this->onSale) {
            $this->schedule(new ShouldMerchandiseBePutOnSale($this->id(), $command->getWhen()), $command->getWhen());
            $this->fire(new SaleDateScheduled($this->id(), $command->getWhen()));
        }
    }

    protected function applySaleDateScheduled(SaleDateScheduled $event)
    {
        $this->putOnSaleOn = $event->getWhen();
    }

    protected function handleShouldMerchandiseBePutOnSale(ShouldMerchandiseBePutOnSale $command)
    {
        if (!$this->onSale && $this->putOnSaleOn == $command->getWhen()) {
            $this->fire(new MerchandiseOnSale($this->id()));
        }
    }

    protected function applyMerchandiseOnSale(MerchandiseOnSale $event)
    {
        $this->onSale = true;
    }

    protected function handleScheduleWithdrawDate(ScheduleWithdrawDate $command)
    {
        $this->schedule(new ShouldMerchandiseBeWithdrawn($this->id(), $command->getWhen()), $command->getWhen());
        $this->fire(new WithdrawDateScheduled($this->id(), $command->getWhen()));
    }

    protected function applyWithdrawDateScheduled(WithdrawDateScheduled $event)
    {
        $this->withdrawOn = $event->getWhen();
    }

    protected function handleShouldMerchandiseBeWithdrawn(ShouldMerchandiseBeWithdrawn $command)
    {
        if ($this->onSale && $this->withdrawOn == $command->getWhen()) {
            $this->fire(new MerchandiseWithdrawnFromSale($this->id()));
        }
    }

    protected function MerchandiseWithdrawnFromSale(MerchandiseWithdrawnFromSale $event)
    {
        $this->onSale = false;
    }
}