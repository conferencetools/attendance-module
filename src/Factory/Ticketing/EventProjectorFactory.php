<?php


namespace ConferenceTools\Attendance\Factory\Ticketing;


use ConferenceTools\Attendance\Domain\Ticketing\EventProjector;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class EventProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $eventRepository = $container->get(RepositoryManager::class)->get(Event::class);
        $ticketRepository = $container->get(RepositoryManager::class)->get(Ticket::class);
        return new EventProjector($eventRepository, $ticketRepository);
    }
}