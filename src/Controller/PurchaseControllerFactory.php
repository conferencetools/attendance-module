<?php

namespace ConferenceTools\Attendance\Controller;

use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\PaymentProvider\PaymentProviderManager;
use ConferenceTools\StripePaymentProvider\PaymentProvider\StripePaymentProvider;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class PurchaseControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //@TODO create a default provider for manual payments
        $paymentProvider = StripePaymentProvider::class;

        $config =  $container->get('Config');
        $paymentProviderName = $config['conferencetools']['purchase_provider'];

        $providerConfig = $config['conferencetools']['payment_providers'][$paymentProviderName]['payment_type'];
        $paymentType = new PaymentType($providerConfig['name'], $providerConfig['timeout'], $providerConfig['manual_confirmation']);

        if (!$paymentType->requiresManualConfirmation()) {
            $paymentProvider = $config['conferencetools']['payment_providers'][$paymentProviderName]['provider_service'];
        }

        $paymentProviderManager = $container->get(PaymentProviderManager::class);
        return new PurchaseController($paymentProviderManager->get($paymentProvider), $paymentType);
    }
}
