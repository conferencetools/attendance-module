<?php


namespace ConferenceTools\Attendance\Factory\Ticketing;


use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Domain\Ticketing\TicketProjector;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class TicketsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(Ticket::class);
        return new TicketProjector($repository);
    }
}