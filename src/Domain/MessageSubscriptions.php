<?php

namespace ConferenceTools\Attendance\Domain;

use ConferenceTools\Attendance\Domain\Delegate;
use ConferenceTools\Attendance\Domain\Discounting\DiscountType;
use ConferenceTools\Attendance\Domain\Purchasing;
use ConferenceTools\Attendance\Domain\Discounting\Command as DiscountingCommand;
use ConferenceTools\Attendance\Domain\Discounting\Event as DiscountingEvent;
use ConferenceTools\Attendance\Domain\Ticketing\Command as TicketingCommand;
use ConferenceTools\Attendance\Domain\Purchasing\Command as PurchasingCommand;
use ConferenceTools\Attendance\Domain\Delegate\Command as DelegateCommand;
use ConferenceTools\Attendance\Domain\Delegate\Event as DelegateEvent;
use ConferenceTools\Attendance\Domain\Ticketing\Event as TicketingEvent;
use ConferenceTools\Attendance\Domain\Purchasing\Event as PurchasingEvent;
use ConferenceTools\Attendance\Domain\Payment\Event as PaymentEvent;
use ConferenceTools\Attendance\Domain\Ticketing\EventProjector;
use ConferenceTools\Attendance\Domain\Ticketing\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\TicketProjector;

class MessageSubscriptions
{
    public static function getSubscriptions()
    {
        return [
            //################## Commands #######################
            DiscountingCommand\CreateDiscount::class => [
                DiscountType::class,
            ],
            DiscountingCommand\AddCode::class => [
                DiscountType::class,
            ],
            DiscountingCommand\CheckDiscountAvailability::class => [
                DiscountType::class,
            ],

            TicketingCommand\ReleaseTicket::class => [
                Ticket::class,
            ],
            TicketingCommand\ScheduleWithdrawDate::class => [
                Ticket::class,
            ],
            TicketingCommand\ScheduleSaleDate::class => [
                Ticket::class,
            ],
            Ticketing\Command\CreateEvent::class => [
                Ticketing\EventActor::class,
            ],
            Ticketing\Command\ShouldTicketBePutOnSale::class => [
                Ticket::class,
            ],
            Ticketing\Command\ShouldTicketBeWithdrawn::class => [
                Ticket::class,
            ],

            //################## Events #######################
            DiscountingEvent\DiscountCreated::class => [
                Discounting\Projector::class,
            ],
            DiscountingEvent\DiscountAvailable::class => [
                Discounting\Projector::class,
            ],
            DiscountingEvent\DiscountWithdrawn::class => [
                Discounting\Projector::class,
            ],
            DiscountingEvent\CodeAdded::class => [
                Discounting\Projector::class,
            ],

            PurchasingEvent\TicketReservationExpired::class => [
                TicketProjector::class,
                EventProjector::class,
            ],
            PurchasingEvent\TicketAllocatedToDelegate::class => [
                Delegate\Projector::class,
            ],
            PurchasingEvent\TicketsReserved::class => [
                TicketProjector::class,
                EventProjector::class,
            ],

            TicketingEvent\TicketsOnSale::class => [
                TicketProjector::class,
            ],
            TicketingEvent\TicketsWithdrawnFromSale::class => [
                TicketProjector::class,
            ],
            TicketingEvent\TicketsReleased::class => [
                TicketProjector::class,
            ],
            TicketingEvent\SaleDateScheduled::class => [
                TicketProjector::class,
            ],
            TicketingEvent\WithdrawDateScheduled::class => [
                TicketProjector::class,
            ],
            Ticketing\Event\EventCreated::class => [
                Ticketing\EventProjector::class,
            ],

            Payment\Event\PaymentRaised::class => [
                Purchasing\Purchase::class,
            ]
        ];
    }
}