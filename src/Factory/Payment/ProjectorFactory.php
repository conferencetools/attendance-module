<?php


namespace ConferenceTools\Attendance\Factory\Payment;


use Phactor\Zend\RepositoryManager;
use ConferenceTools\Attendance\Domain\Payment\Projector;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(Payment::class);
        return new Projector($repository);
    }
}