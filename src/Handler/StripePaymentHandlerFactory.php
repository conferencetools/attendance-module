<?php

namespace ConferenceTools\Attendance\Handler;

use Cartalyst\Stripe\Stripe;
use Phactor\Identity\Generator;
use Phactor\Message\Bus;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class StripePaymentHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        Stripe::disableAmountConverter();
        $stripeClient = Stripe::make($config['zfr_stripe']['secret_key']);

        return new StripePaymentHandler($stripeClient, $container->get(Bus::class), $container->get(Generator::class));
    }
}