<?php

namespace ConferenceTools\Attendance\Handler;

use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\Mail\Transport\Factory;
use Zend\ServiceManager\Factory\FactoryInterface;

class EmailTicketFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $transport = Factory::create($config['mail']);
        $repositoryManager = $container->get(RepositoryManager::class);

        $emailConfig = $config['conferencetools']['mailconf']['ticket'] ?? [];
        $emailConfig['companyinfo'] = $config['conferencetools']['companyinfo'];
        return new EmailTicket(
            $repositoryManager->get(Delegate::class),
            $container->get('Zend\View\View'),
            $transport,
            $emailConfig
        );
    }
}
