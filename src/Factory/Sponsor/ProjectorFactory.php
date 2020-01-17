<?php

namespace ConferenceTools\Attendance\Factory\Sponsor;

use ConferenceTools\Attendance\Domain\Sponsor\Projector;
use ConferenceTools\Attendance\Domain\Sponsor\ReadModel\Sponsor;
use Interop\Container\ContainerInterface;
use Phactor\Identity\Generator;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(Sponsor::class);
        return new Projector($repository, $container->get(Generator::class));
    }
}