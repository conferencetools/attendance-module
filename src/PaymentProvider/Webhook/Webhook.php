<?php


namespace ConferenceTools\Attendance\PaymentProvider\Webhook;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Webhook
{
    /** @ORM\Column(type="string") @ORM\Id() */
    private $route;
    /** @ORM\Column(type="string") */
    private $secret;
    /** @ORM\Column(type="string") */
    private $baseUrl;
    /** @ORM\Column(type="string") */
    private $webhookId;

    public function __construct(string $baseUrl, string $route, string $webhookId, string $secret)
    {
        $this->route = $route;
        $this->secret = $secret;
        $this->baseUrl = $baseUrl;
        $this->webhookId = $webhookId;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getWebhookId(): string
    {
        return $this->webhookId;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}