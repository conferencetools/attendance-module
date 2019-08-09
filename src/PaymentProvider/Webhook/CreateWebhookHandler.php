<?php


namespace ConferenceTools\Attendance\PaymentProvider\Webhook;


use Cartalyst\Stripe\Stripe;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;
use Zend\Router\RouteStackInterface;
use Zend\Uri\Uri;

class CreateWebhookHandler implements Handler
{
    private $stripeClient;
    private $router;
    private $webhookRepository;

    public function __construct(Stripe $stripeClient, RouteStackInterface $router, Repository $webhookRepository)
    {
        $this->stripeClient = $stripeClient;
        $this->router = $router;
        $this->webhookRepository = $webhookRepository;
    }

    public function handle(DomainMessage $message)
    {
        $command = $message->getMessage();
        if (!($command instanceof CreateWebhook)) {
            return;
        }

        $route = $this->router->assemble([], ['name' => $command->getRoute()]);
        $webhookEndpoint = Uri::merge($command->getBaseUri(), $route)->toString();

        // @TODO enable passing of events in the command so we can handle other things in the future
        $endpoint = $this->stripeClient->webhookEndpoints()->create([
            'url' => $webhookEndpoint,
            'enabled_events' => ['payment_intent.succeeded'],
            'connect' => false,
        ]);

        $webhook = new Webhook($command->getBaseUri(), $command->getRoute(), $endpoint['id'], $endpoint['secret']);

        $this->webhookRepository->add($webhook);
        $this->webhookRepository->commit();
    }
}