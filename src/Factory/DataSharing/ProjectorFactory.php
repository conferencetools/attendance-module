<?php

namespace ConferenceTools\Attendance\Factory\DataSharing;

use ConferenceTools\Attendance\Domain\DataSharing\Projector;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ProjectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(DelegateList::class);
        return new Projector($repository);
    }
}
