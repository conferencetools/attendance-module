<?php

namespace ConferenceTools\Attendance\Handler;

use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\TicketsForSale;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\Mail\Transport\Factory;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmailPurchaseFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $transport = Factory::create($config['mail']);
        $repositoryManager = $container->get(RepositoryManager::class);

        return new EmailPurchase(
            $repositoryManager->get(Purchase::class),
            $repositoryManager->get(TicketsForSale::class),
            $container->get('Zend\View\View'),
            $transport,
            $config['conferencetools']['mailconf']['purchase'] ?? []
        );
    }
}
