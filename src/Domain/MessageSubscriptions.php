<?php

namespace ConferenceTools\Attendance\Domain;

use ConferenceTools\Attendance\Domain\Delegate\Delegate;
use ConferenceTools\Attendance\Domain\Purchasing\Projector;
use ConferenceTools\Attendance\Domain\Purchasing\Purchase;
use ConferenceTools\Attendance\Domain\Ticketing\AvailableTickets;
use ConferenceTools\Attendance\Domain\Ticketing\Command as TicketingCommand;
use ConferenceTools\Attendance\Domain\Purchasing\Command as PurchasingCommand;
use ConferenceTools\Attendance\Domain\Delegate\Command as DelegateCommand;
use ConferenceTools\Attendance\Domain\Ticketing\Event as TicketingEvent;
use ConferenceTools\Attendance\Domain\Purchasing\Event as PurchasingEvent;
use ConferenceTools\Attendance\Domain\Ticketing\TicketType;

class MessageSubscriptions
{
    public static function getSubscriptions()
    {
        return [
            DelegateCommand\RegisterDelegate::class => [
                Delegate::class,
            ],
            PurchasingCommand\CheckPurchaseTimeout::class => [
                Purchase::class,
            ],
            PurchasingCommand\PurchaseTickets::class => [
                Purchase::class,
            ],
            PurchasingCommand\AllocateTicketToDelegate::class => [
                Purchase::class,
            ],
            TicketingCommand\ReleaseTicket::class => [
                TicketType::class,
            ],
            TicketingCommand\CheckTicketAvailability::class => [
                TicketType::class,
            ],

            PurchasingEvent\TicketReservationExpired::class => [
                AvailableTickets::class,
            ],
            PurchasingEvent\TicketAllocatedToDelegate::class => [
                Delegate::class,
            ],
            PurchasingEvent\TicketsReserved::class => [
                AvailableTickets::class,
                Projector::class,
            ],
            TicketingEvent\TicketsOnSale::class => [
                AvailableTickets::class,
            ],
            TicketingEvent\TicketsWithdrawnFromSale::class => [
                AvailableTickets::class,
            ]
        ];
    }
}