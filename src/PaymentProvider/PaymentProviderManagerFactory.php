<?php

namespace ConferenceTools\Attendance\PaymentProvider;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PaymentProviderManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        return new PaymentProviderManager($container, $config['payment_providers']);
    }
}
