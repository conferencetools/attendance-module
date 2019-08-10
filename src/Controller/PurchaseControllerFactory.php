<?php

namespace ConferenceTools\Attendance\Controller;

use ConferenceTools\Attendance\PaymentProvider\PaymentProviderManager;
use ConferenceTools\StripePaymentProvider\PaymentProvider\StripePaymentProvider;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $paymentProviderManager = $container->get(PaymentProviderManager::class);
        return new PurchaseController($paymentProviderManager->get(StripePaymentProvider::class));
    }
}
