<?php


namespace ConferenceTools\Attendance\PaymentProvider;


use Cartalyst\Stripe\Stripe;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use Interop\Container\ContainerInterface;
use Phactor\Zend\RepositoryManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class StripePaymentHandlerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        Stripe::disableAmountConverter();
        $stripeClient = Stripe::make($config['zfr_stripe']['secret_key']);

        $repositoryManager = $container->get(RepositoryManager::class);

        return new StripePaymentHandler(
            $repositoryManager->get(Payment::class),
            $repositoryManager->get(StripePayment::class),
            $stripeClient
        );
    }
}