<?php

namespace ConferenceTools\Attendance\Controller;

use ConferenceTools\Attendance\Controller\Admin\PurchaseController;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use Doctrine\Common\Collections\Criteria;
use Phactor\ReadModel\Repository;
use Phactor\Zend\ControllerPlugin\MessageBus;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Plugin\FlashMessenger\FlashMessenger;

/**
 * @method MessageBus messageBus()
 * @method Repository repository(string $className)
 * @method FlashMessenger flashMessenger()
 * @method \Zend\Form\Form form(string $name, array $options = [])
 */
abstract class AppController extends AbstractActionController
{
    protected $tickets;

    /**
     * @return Ticket[]
     */
    protected function getTickets(): array
    {
        if ($this->tickets === null) {
            $tickets = $this->repository(Ticket::class)->matching(new Criteria());
            $ticketsIndexed = [];

            foreach ($tickets as $ticket) {
                $ticketsIndexed[$ticket->getId()] = $ticket;
            }

            $this->tickets = $ticketsIndexed;
        }

        return $this->tickets;
    }

    protected function indexBy(iterable $entities, string $by = 'getId'): array
    {
        $indexed = [];
        foreach ($entities as $entity) {
            $indexed[$entity->$by()] = $entity;
        }

        return $indexed;
    }
}