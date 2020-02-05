<?php

namespace ConferenceTools\Attendance\Factory\DataSharing;

use ConferenceTools\Attendance\Domain\DataSharing\NotifiesDelegates;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use Interop\Container\ContainerInterface;
use Phactor\Identity\Generator;
use Phactor\Message\Bus;
use Phactor\Message\MessageFirer;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class NotifiesDelegatesFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $messageBus = $container->get(Bus::class);
        $identityGenerator = $container->get(Generator::class);

        return new NotifiesDelegates(
            $container->get(RepositoryManager::class)->get(DelegateList::class),
            $container->get(RepositoryManager::class)->get(Delegate::class),
            new MessageFirer($identityGenerator, $messageBus)
        );
    }
}