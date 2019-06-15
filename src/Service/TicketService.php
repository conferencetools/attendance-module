<?php

namespace ConferenceTools\Attendance\Service;

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

        $total = 0;
        foreach ($quantities as $ticketId => $quantity) {
            if ((int) $quantity > 0) { //filter out any rows which haven't been selected
                $total += $quantity;

                if (!isset($tickets[$ticketId]) || $tickets[$ticketId]->getRemaining() < $quantity) {
                    return new TicketValidationFailed('One or more of the tickets you selected has sold out or you have selected more than the quantity remaining');
                }
            }
        }

        if ($total < 1) {
            return new TicketValidationFailed('Please select at least one ticket to purchase');
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
