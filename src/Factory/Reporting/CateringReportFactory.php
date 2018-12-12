<?php

namespace ConferenceTools\Attendance\Factory\Reporting;

use ConferenceTools\Attendance\Domain\Reporting\CateringReport;
use ConferenceTools\Attendance\Domain\Reporting\ReadModel\DelegateCatering;
use Phactor\Zend\RepositoryManager;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CateringReportFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repository = $container->get(RepositoryManager::class)->get(DelegateCatering::class);
        return new CateringReport($repository);
    }
}