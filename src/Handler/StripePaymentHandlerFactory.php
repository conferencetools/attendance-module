<?php


namespace ConferenceTools\Attendance\Handler;


use Carnage\Phactor\Message\Bus;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use ZfrStripe\Client\StripeClient;

class StripePaymentHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new StripePaymentHandler($container->get(StripeClient::class), $container->get(Bus::class));
    }
}