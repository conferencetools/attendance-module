<?php


namespace ConferenceTools\Attendance\Factory\Ticketing;


use Carnage\Phactor\Zend\RepositoryManager;
use ConferenceTools\Attendance\Domain\Ticketing\AvailableTickets;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\TicketType;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class AvailableTicketsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(TicketType::class);
        return new AvailableTickets($repository);
    }
}