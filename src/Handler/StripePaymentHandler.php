<?php


namespace ConferenceTools\Attendance\Handler;


use Carnage\Phactor\Message\DomainMessage;
use Carnage\Phactor\Message\Handler;
use ZfrStripe\Client\StripeClient;

class StripePaymentHandler implements Handler
{
    private $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function handle(DomainMessage $domainMessage)
    {
        $message = $domainMessage->getMessage();

        $this->stripeClient->createCharge([
            "amount" => $purchase->getTotalCost()->getGross()->getAmount(),
            "currency" => $purchase->getTotalCost()->getGross()->getCurrency(),
            'source' => $data['stripe_token'],
            'metadata' => [
                'email' => $data['purchase_email'],
                'purchaseId' => $purchaseId
            ]
        ]);
    }
}