<?php

namespace ConferenceTools\Attendance\Service;

use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use Doctrine\Common\Collections\Criteria;
use Phactor\ReadModel\Repository;

class TicketService
{
    private $ticketsRepository;
    private $eventsRepository;
    private $tickets;

    public function __construct(Repository $ticketsRepository, Repository $eventsRepository)
    {
        $this->ticketsRepository = $ticketsRepository;
        $this->eventsRepository = $eventsRepository;
    }

    public function getTicketsForPurchasePage(): array
    {
        $tickets = $this->getTickets(true);

        $ticketsByEvent = [];
        $eventIds = [];

        foreach ($tickets as $ticket) {
            /** @var Ticket $ticket */
            $eventId = $ticket->getEventId();
            $eventIds[$eventId] = $eventId;
            $ticketsByEvent[$eventId][] = $ticket;
        }

        $events = $this->eventsRepository->matching(Criteria::create()->where(Criteria::expr()->in('id', $eventIds))->orderBy(['startsOn' => Criteria::ASC]));
        $events = $this->indexBy($events);
        uasort($events, function (Event $a, Event $b) { return $a->getStartsOn() <=> $b->getStartsOn();} );
        return ['tickets' => $ticketsByEvent, 'events' => $events];
    }

    public function getTickets($onlyOnSale = false): array
    {
        if ($this->tickets === null) {
            $criteria = Criteria::create();
            if ($onlyOnSale) {
                $criteria->where(Criteria::expr()->eq('onSale', true));
            }
            $tickets = $this->ticketsRepository->matching($criteria);
            $this->tickets = $this->indexBy($tickets);
        }

        return $this->tickets;
    }

    public function validateTicketQuantity(array $quantities): TicketValidation
    {
        $tickets = $this->getTickets(true);

        $eventTickets = [];
        $total = 0;
        foreach ($quantities as $ticketId => $quantity) {
            if ((int) $quantity > 0) { //filter out any rows which haven't been selected
                $total += $quantity;

                if (!isset($tickets[$ticketId]) || $tickets[$ticketId]->getRemaining() < $quantity) {
                    return new TicketValidationFailed('One or more of the tickets you selected has sold out or you have selected more than the quantity remaining');
                }

                $eventId = $tickets[$ticketId]->getEventId();
                $eventTickets[$eventId] = isset($eventTickets[$eventId]) ? $eventTickets[$eventId] + $quantity : $quantity;
            }
        }

        if ($total < 1) {
            return new TicketValidationFailed('Please select at least one ticket to purchase');
        }

        $events = $this->eventsRepository->matching(Criteria::create()->where(Criteria::expr()->in('id', array_keys($eventTickets))));
        foreach ($events as $event) {
            /** @var Event $event */
            if ($eventTickets[$event->getId()] > $event->getRemainingCapacity()) {
                return new TicketValidationFailed('The tickets you have selected would put the event over capacity, please reduce the number of tickets you have selected');
            }
        }

        return new class () implements TicketValidation {
            public function getReason(): string
            {
                return '';
            }
        };
    }

    private function indexBy(iterable $entities, string $by = 'getId'): array
    {
        $indexed = [];
        foreach ($entities as $entity) {
            $indexed[$entity->$by()] = $entity;
        }

        return $indexed;
    }
}
