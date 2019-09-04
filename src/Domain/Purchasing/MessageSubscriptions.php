<?php


namespace ConferenceTools\Attendance\Domain\Purchasing;



class MessageSubscriptions
{
    public static function getSubscriptions(): array
    {
        return [
            // ######## external events ########


            // ######## purchase commands ########
            Command\CheckPurchaseTimeout::class => [
                Purchase::class,
            ],
            Command\PurchaseItems::class => [
                Purchase::class,
            ],
            Command\AllocateTicketToDelegate::class => [
                Purchase::class,
            ],
            Command\ApplyDiscount::class => [
                Purchase::class,
            ],
            Command\Checkout::class => [
                Purchase::class,
            ],

            // ######## purchase events ########
            Event\TicketReservationExpired::class => [
                Projector::class,
            ],
            Event\TicketsReserved::class => [
                Projector::class,
            ],
            Event\PurchaseStartedBy::class => [
                Projector::class,
            ],
            Event\OutstandingPaymentCalculated::class => [
                Projector::class,
            ],
            Event\DiscountApplied::class => [
                Projector::class,
            ],
            Event\PurchaseCompleted::class => [
                Projector::class,
            ],
            Event\MerchandiseAddedToPurchase::class => [
                Projector::class,
            ],
            Event\MerchandisePurchaseExpired::class => [
                Projector::class,
            ],
        ];
    }
}