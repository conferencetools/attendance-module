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
use ConferenceTools\Attendance\Domain\Ticketing\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\Tickets;

class MessageSubscriptions
{
    public static function getSubscriptions()
    {
        return [
            //################## Commands #######################
            DelegateCommand\RegisterDelegate::class => [
                Delegate\Delegate::class,
            ],
            DelegateCommand\UpdateDelegateDetails::class => [
                Delegate\Delegate::class,
            ],
            DelegateCommand\CheckIn::class => [
                Delegate\Delegate::class,
            ],

            DiscountingCommand\CreateDiscount::class => [
                DiscountType::class,
            ],
            DiscountingCommand\AddCode::class => [
                DiscountType::class,
            ],
            DiscountingCommand\CheckDiscountAvailability::class => [
                DiscountType::class,
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
            PurchasingCommand\ApplyDiscount::class => [
                Purchasing\Purchase::class,
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
            DelegateEvent\DelegateRegistered::class => [
                Delegate\Projector::class,
            ],
            DelegateEvent\DelegateDetailsUpdated::class => [
                Delegate\Projector::class,
            ],
            DelegateEvent\CheckedIn::class => [
                Delegate\Projector::class,
            ],

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
                Tickets::class,
                Purchasing\Projector::class,
            ],
            PurchasingEvent\TicketAllocatedToDelegate::class => [
                Delegate\Delegate::class,
                Delegate\Projector::class,
            ],
            PurchasingEvent\TicketsReserved::class => [
                Tickets::class,
                Purchasing\Projector::class,
            ],
            PurchasingEvent\PurchaseStartedBy::class => [
                Purchasing\Projector::class,
            ],
            PurchasingEvent\OutstandingPaymentCalculated::class => [
                Purchasing\Projector::class,
            ],
            Purchasing\Event\DiscountApplied::class => [
                Purchasing\Projector::class,
            ],

            TicketingEvent\TicketsOnSale::class => [
                Tickets::class,
            ],
            TicketingEvent\TicketsWithdrawnFromSale::class => [
                Tickets::class,
            ],
            TicketingEvent\TicketsReleased::class => [
                Tickets::class,
            ],
            TicketingEvent\SaleDateScheduled::class => [
                Tickets::class,
            ],
            TicketingEvent\WithdrawDateScheduled::class => [
                Tickets::class,
            ],
            Ticketing\Event\EventCreated::class => [
                Ticketing\EventProjector::class,
            ],

            PaymentEvent\PaymentMade::class => [
                Purchasing\Purchase::class,
                Purchasing\Projector::class,
                Delegate\Projector::class,
            ]
        ];
    }
}