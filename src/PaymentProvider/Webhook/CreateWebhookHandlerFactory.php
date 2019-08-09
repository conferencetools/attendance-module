<?php

namespace ConferenceTools\Attendance\PaymentProvider\Webhook;

use Cartalyst\Stripe\Stripe;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class CreateWebhookHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        Stripe::disableAmountConverter();
        $stripeClient = Stripe::make($config['zfr_stripe']['secret_key']);

        return new CreateWebhookHandler(
            $stripeClient,
            $container->get('Router'),
            $container->get(RepositoryManager::class)->get(Webhook::class)
        );
    }
}