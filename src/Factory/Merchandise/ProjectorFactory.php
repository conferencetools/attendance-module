<?php

namespace ConferenceTools\Attendance\Factory\Merchandise;

use ConferenceTools\Attendance\Domain\Merchandise\MerchandiseProjector;
use ConferenceTools\Attendance\Domain\Merchandise\ReadModel\Merchandise;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(Merchandise::class);
        return new MerchandiseProjector($repository);
    }
}
