<?php


namespace ConferenceTools\Attendance\PaymentProvider;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class StripePayment
{
    /** @ORM\Column(type="string") @ORM\Id() */
    private $id;
    /** @ORM\Column(type="string") */
    private $paymentIntentId;
    /** @ORM\Column(type="string") */
    private $clientSecret;

    public function __construct(string $id, string $paymentIntentId, string $clientSecret)
    {
        $this->id = $id;
        $this->paymentIntentId = $paymentIntentId;
        $this->clientSecret = $clientSecret;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPaymentIntentId(): string
    {
        return $this->paymentIntentId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }
}