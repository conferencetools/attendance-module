<?php

namespace ConferenceTools\Attendance\Controller;

use ConferenceTools\Attendance\Controller\Admin\PurchaseController;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Service\TicketService;
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
    protected $ticketsService;

    /**
     * @return Ticket[]
     */
    protected function getTickets($onlyOnSale = false): array
    {
        return $this->getTicketService()->getTickets($onlyOnSale);
    }

    protected function indexBy(iterable $entities, string $by = 'getId'): array
    {
        $indexed = [];
        foreach ($entities as $entity) {
            $indexed[$entity->$by()] = $entity;
        }

        return $indexed;
    }

    protected function getTicketService(): TicketService
    {
        if (!isset($this->ticketsService)) {
            $this->ticketsService = new TicketService(
                $this->repository(Ticket::class),
                $this->repository(Event::class)
            );
        }

        return $this->ticketsService;
    }
}