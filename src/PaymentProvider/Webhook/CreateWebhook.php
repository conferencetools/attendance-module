<?php


namespace ConferenceTools\Attendance\PaymentProvider\Webhook;


class CreateWebhook
{
    private $route;
    private $baseUri;

    public function __construct(string $route, string $baseUri)
    {
        $this->route = $route;
        $this->baseUri = $baseUri;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }
}