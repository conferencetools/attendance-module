<?php

namespace ConferenceTools\Attendance\Domain;

use ConferenceTools\Attendance\Domain\Delegate;
use ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Ticketing\AvailableTickets;
use ConferenceTools\Attendance\Domain\Ticketing\Command as TicketingCommand;
use ConferenceTools\Attendance\Domain\Purchasing\Command as PurchasingCommand;
use ConferenceTools\Attendance\Domain\Delegate\Command as DelegateCommand;
use ConferenceTools\Attendance\Domain\Delegate\Event as DelegateEvent;
use ConferenceTools\Attendance\Domain\Ticketing\Event as TicketingEvent;
use ConferenceTools\Attendance\Domain\Purchasing\Event as PurchasingEvent;
use ConferenceTools\Attendance\Domain\Payment\Event as PaymentEvent;
use ConferenceTools\Attendance\Domain\Ticketing\TicketType;

class MessageSubscriptions
{
    public static function getSubscriptions()
    {
        return [
            //################## Commands #######################
            DelegateCommand\RegisterDelegate::class => [
                Delegate\Delegate::class,
            ],

            PurchasingCommand\CheckPurchaseTimeout::class => [
                Purchasing\Purchase::class,
            ],
            PurchasingCommand\PurchaseTickets::class => [
                Purchasing\Purchase::class,
            ],
            PurchasingCommand\AllocateTicketToDelegate::class => [
                Purchasing\Purchase::class,
            ],

            TicketingCommand\ReleaseTicket::class => [
                TicketType::class,
            ],
            TicketingCommand\CheckTicketAvailability::class => [
                TicketType::class,
            ],

            //################## Events #######################
            DelegateEvent\DelegateRegistered::class => [
                Delegate\Projector::class,
            ],

            PurchasingEvent\TicketReservationExpired::class => [
                AvailableTickets::class,
            ],
            PurchasingEvent\TicketAllocatedToDelegate::class => [
                Delegate\Delegate::class,
                Delegate\Projector::class,
            ],
            PurchasingEvent\TicketsReserved::class => [
                AvailableTickets::class,
                Purchasing\Projector::class,
            ],
            PurchasingEvent\PurchaseStartedBy::class => [
                Purchasing\Projector::class,
            ],
            PurchasingEvent\OutstandingPaymentCalculated::class => [
                Purchasing\Projector::class,
            ],

            TicketingEvent\TicketsOnSale::class => [
                AvailableTickets::class,
            ],
            TicketingEvent\TicketsWithdrawnFromSale::class => [
                AvailableTickets::class,
            ],

            PaymentEvent\PaymentMade::class => [
                Purchasing\Purchase::class,
            ]
        ];
    }
}