<?php


namespace ConferenceTools\Attendance\Handler;


use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Stripe;
use Phactor\Identity\Generator;
use Phactor\Message\ActorIdentity;
use Phactor\Message\Bus;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use ConferenceTools\Attendance\Domain\Payment\Command\TakePayment;
use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;

class StripePaymentHandler implements Handler
{
    private static $cardErrorMessages = [
        'invalid_number' => 'The card number is not a valid credit card number.',
        'invalid_expiry_month' => 'The card\'s expiration month is invalid.',
        'invalid_expiry_year' => 'The card\'s expiration year is invalid.',
        'invalid_cvc' => 'The card\'s security code/CVC is invalid.',
        'invalid_swipe_data' => 'The card\'s swipe data is invalid.',
        'incorrect_number' => 'The card number is incorrect.',
        'expired_card' => 'The card has expired.',
        'incorrect_cvc' => 'The card\'s security code/CVC is incorrect.',
        'incorrect_zip' => 'The address for your card did not match the card\'s billing address.',
        'card_declined' => 'The card was declined.',
        'missing' => 'There is no card on a customer that is being charged.',
        'processing_error' => 'An error occurred while processing the card.',
    ];

    private $stripeClient;
    private $messageBus;
    private $identityGenerator;
    private $currency = 'GBP';

    public function __construct(Stripe $stripeClient, Bus $messageBus, Generator $identityGenerator)
    {
        $this->stripeClient = $stripeClient;
        $this->messageBus = $messageBus;
        $this->identityGenerator = $identityGenerator;
    }

    public function handle(DomainMessage $domainMessage)
    {
        /** @var TakePayment $message */
        $message = $domainMessage->getMessage();
        try {
            $this->stripeClient->charges()->create([
                "amount" => $message->getAmount()->getGross()->getAmount(),
                "currency" => $this->currency,
                'source' => $message->getPaymentData(),
                'metadata' => [
                    'email' => $message->getPaymentEmail(),
                    'purchaseId' => $message->getPurchaseId(),
                ]
            ]);
        } catch (CardErrorException $e) {
            throw new PaymentFailed($this->getDetailedErrorMessage($e));
        }

        $this->messageBus->handle(DomainMessage::recordMessage(
            $this->identityGenerator->generateIdentity(),
            $domainMessage,
            new ActorIdentity(\get_class($this), $this->identityGenerator->generateIdentity()),
            1,
            new PaymentMade($message->getPurchaseId())
        ));
    }

    private function getDetailedErrorMessage(CardErrorException $e)
    {
        $errors = $e->getRawOutput();
        $code = isset($errors['error']['code']) ? $errors['error']['code'] : 'processing_error';
        $code = isset(static::$cardErrorMessages[$code]) ? $code : 'processing_error';

        return static::$cardErrorMessages[$code];
    }
}