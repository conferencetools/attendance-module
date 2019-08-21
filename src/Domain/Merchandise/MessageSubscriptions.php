<?php


namespace ConferenceTools\Attendance\Domain\Merchandise;

class MessageSubscriptions
{
    public static function getSubscriptions(): array
    {
        return [
            // ######## external events ########

            // ######## merchandise commands ########
            Command\CreateMerchandise::class => [
                Merchandise::class,
            ],
            Command\ScheduleSaleDate::class => [
                Merchandise::class,
            ],
            Command\ScheduleWithdrawDate::class => [
                Merchandise::class,
            ],
            Command\ShouldMerchandiseBePutOnSale::class => [
                Merchandise::class,
            ],
            Command\ShouldMerchandiseBeWithdrawn::class => [
                Merchandise::class,
            ],

            // ######## merchandise events ########
            Event\MerchandiseCreated::class => [
                MerchandiseProjector::class,
            ],
            Event\MerchandiseOnSale::class => [
                MerchandiseProjector::class,
            ],
            Event\MerchandiseWithdrawnFromSale::class => [
                MerchandiseProjector::class,
            ],
            Event\SaleDateScheduled::class => [
                MerchandiseProjector::class,
            ],
            Event\WithdrawDateScheduled::class => [
                MerchandiseProjector::class,
            ],
        ];
    }
}