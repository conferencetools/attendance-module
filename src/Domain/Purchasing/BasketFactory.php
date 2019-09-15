<?php

namespace ConferenceTools\Attendance\Domain\Purchasing;

use ConferenceTools\Attendance\Domain\Merchandise\ReadModel\Merchandise;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use Doctrine\Common\Collections\Criteria;
use Phactor\ReadModel\Repository;

class BasketFactory
{
    private $ticketRepository;
    private $merchandiseRepository;
    private $eventRepository;
    /** @var Ticket[] */
    private $ticketCache;
    /** @var Merchandise[] */
    private $merchandiseCache;

    public function __construct(Repository $ticketRepository, Repository $merchandiseRepository, Repository $eventRepository)
    {
        $this->ticketRepository = $ticketRepository;
        $this->merchandiseRepository = $merchandiseRepository;
        $this->eventRepository = $eventRepository;
    }

    public function createBasket(array $tickets, array $merchandises): Basket
    {
        $totalTickets = 0;
        $ticketQuantities = [];
        $eventQuantities = [];
        $merchandiseQuantities = [];
        $skipTicketCheck = false;

        foreach ($tickets as $ticketId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity > 0) {
                $ticket = $this->getTicket($ticketId);

                if (!$ticket->isOnSale() || $ticket->getRemaining() < $quantity) {
                    throw new \DomainException('One or more of the tickets you selected has sold out or you have selected more than the quantity remaining');
                }

                $totalTickets += $quantity;
                $ticketQuantities[] = new TicketQuantity($ticketId, $quantity, $ticket->getPrice());
                $eventQuantities[$ticket->getEventId()] = isset($eventQuantities[$ticket->getEventId()]) ? $eventQuantities[$ticket->getEventId()] + $quantity : $quantity;
            }
        }

        $events = $this->eventRepository->matching(Criteria::create()->where(Criteria::expr()->in('id', array_keys($eventQuantities))));

        foreach ($events as $event) {
            /** @var Event $event */
            if ($eventQuantities[$event->getId()] > $event->getRemainingCapacity()) {
                throw new \DomainException('The tickets you have selected would put the event over capacity, please reduce the number of tickets you have selected');
            }
        }

        foreach ($merchandises as $merchandiseId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity > 0) {
                $merchandise = $this->getMerchandise($merchandiseId);

                if (!$merchandise->isOnSale() || $merchandise->getRemaining() < $quantity) {
                    throw new \DomainException('One or more of the merchandise you selected has sold out or you have selected more than the quantity remaining');
                }

                $merchandiseQuantities[] = new MerchandiseQuantity($merchandiseId, $quantity, $merchandise->getPrice());
                if (!$merchandise->requiresTicket()) {
                    $skipTicketCheck = true;
                }
            }
        }

        if (!$skipTicketCheck && $totalTickets < 1) {
            throw new \DomainException('Please select at least one ticket to purchase');
        }

        return new Basket($ticketQuantities, $merchandiseQuantities);
    }

    private function getTicket(string $ticketId): Ticket
    {
        if ($this->ticketCache === null) {
            $tickets = $this->ticketRepository->matching(Criteria::create());
            $this->ticketCache = [];
        
            foreach ($tickets as $ticket) {
                $this->ticketCache[$ticket->getId()] = $ticket;
            }
        } 
        
        if (!isset($this->ticketCache[$ticketId])) {
            throw new \DomainException('One or more of the tickets you selected has sold out or you have selected more than the quantity remaining');
        }
        
        return $this->ticketCache[$ticketId];
    }

    private function getMerchandise(string $merchandiseId): Merchandise
    {
        if ($this->merchandiseCache === null) {
            $merchandises = $this->merchandiseRepository->matching(Criteria::create());
            $this->merchandiseCache = [];

            foreach ($merchandises as $merchandise) {
                $this->merchandiseCache[$merchandise->getId()] = $merchandise;
            }
        }

        if (!isset($this->merchandiseCache[$merchandiseId])) {
            throw new \DomainException('One or more of the merchandise you selected has sold out or you have selected more than the quantity remaining');
        }

        return $this->merchandiseCache[$merchandiseId];
    }
}