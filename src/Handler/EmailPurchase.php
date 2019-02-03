<?php

namespace ConferenceTools\Attendance\Handler;

use ConferenceTools\Attendance\Domain\Payment\Event\PaymentMade;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use Doctrine\Common\Collections\Criteria;
use Phactor\Message\DomainMessage;
use Phactor\Message\Handler;
use Phactor\ReadModel\Repository;
use Zend\Http\Response;
use Zend\Mail\Message;
use Zend\Mail\Transport\TransportInterface;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\View\Model\ViewModel;
use Zend\View\View;

class EmailPurchase implements Handler
{
    private $view;
    private $mail;
    private $config;
    private $purchaseRepository;
    private $ticketsRepository;
    private $discountRepository;

    public function __construct(
        Repository $purchaseRepository,
        Repository $ticketsRepository,
        Repository $discountRepository,
        View $view,
        TransportInterface $mail,
        array $config = []
    ) {
        $this->view = $view;
        $this->mail = $mail;
        $this->config = $config;
        $this->config['subject'] = $this->config['subject'] ?? 'Your ticket receipt';
        $this->purchaseRepository = $purchaseRepository;
        $this->ticketsRepository = $ticketsRepository;
        $this->discountRepository = $discountRepository;
    }


    public function handleDomainMessage(DomainMessage $message)
    {
        $this->handle($message->getEvent());
    }

    public function handle(DomainMessage $domainMessage)
    {
        $message = $domainMessage->getMessage();
        if (!($message instanceof PaymentMade)) {
            return;
        }
        /** @var Purchase $purchase */
        $purchase = $this->purchaseRepository->get($message->getActorId());
        $discount = null;

        if ($purchase->getDiscountId() !== null) {
            $discount = $this->discountRepository->get($purchase->getDiscountId());
        }

        $tickets = $this->ticketsRepository->matching(Criteria::create());

        foreach ($tickets as $ticket) {
            $ticketsIndexed[$ticket->getId()] = $ticket;
        }

        $viewModel = new ViewModel(['purchase' => $purchase, 'discount' => $discount, 'tickets' => $ticketsIndexed, 'config'=> $this->config]);
        $viewModel->setTemplate('email/receipt');

        $response = new Response();
        $this->view->setResponse($response);
        $this->view->render($viewModel);
        $html = $response->getContent();

        $emailMessage = $this->buildMessage($html);
        $emailMessage->setTo($purchase->getEmail());

        $this->mail->send($emailMessage);
    }

    private function buildMessage($htmlMarkup)
    {
        $html = new MimePart($htmlMarkup);
        $html->setCharset('UTF-8');
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->setParts(array($html));

        $message = new Message();
        $message->setBody($body);
        $message->setSubject($this->config['subject']);
        if (isset($this->config['from'])) {
            $message->setFrom($this->config['from']);
        }
        $message->setEncoding('UTF-8');

        return $message;
    }
}
