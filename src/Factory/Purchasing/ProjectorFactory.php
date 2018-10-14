<?php


namespace ConferenceTools\Attendance\Factory\Purchasing;


use Carnage\Phactor\Zend\RepositoryManager;
use ConferenceTools\Attendance\Domain\Purchasing\Projector;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(Purchase::class);
        return new Projector($repository);
    }
}