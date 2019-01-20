<?php


namespace ConferenceTools\Attendance\Factory\Discounting;


use ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountType;
use Phactor\Zend\RepositoryManager;
use ConferenceTools\Attendance\Domain\Discounting\Projector;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(DiscountType::class);
        return new Projector($repository);
    }
}