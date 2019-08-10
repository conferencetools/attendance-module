<?php

namespace ConferenceTools\Attendance\Factory\Delegate;

use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use Phactor\Zend\RepositoryManager;
use ConferenceTools\Attendance\Domain\Delegate\Projector;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(Delegate::class);
        return new Projector($repository);
    }
}