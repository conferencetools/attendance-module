<?php

namespace ConferenceTools\Attendance\Factory\Payment;

use ConferenceTools\Attendance\Domain\Payment\ZeroPaymentHandler;
use Interop\Container\ContainerInterface;
use Phactor\Identity\Generator;
use Phactor\Message\Bus;
use Phactor\Message\MessageFirer;
use Zend\ServiceManager\Factory\FactoryInterface;

class ZeroPaymentHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $messageBus = $container->get(Bus::class);
        $identityGenerator = $container->get(Generator::class);

        return new ZeroPaymentHandler(new MessageFirer($identityGenerator, $messageBus));
    }
}
