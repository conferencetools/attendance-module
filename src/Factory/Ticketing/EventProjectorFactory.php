<?php


namespace ConferenceTools\Attendance\Factory\Ticketing;


use ConferenceTools\Attendance\Domain\Ticketing\EventProjector;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class EventProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(Event::class);
        return new EventProjector($repository);
    }
}