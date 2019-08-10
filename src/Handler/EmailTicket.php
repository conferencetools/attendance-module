<?php

namespace ConferenceTools\Attendance\Handler;

use ConferenceTools\Attendance\Domain\Delegate\Event\CheckinIdGenerated;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
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

class EmailTicket implements Handler
{
    private $view;
    private $mail;
    private $config;
    private $delegateRepository;

    public function __construct(
        Repository $delegateRepository,
        View $view,
        TransportInterface $mail,
        array $config = []
    ) {
        $this->view = $view;
        $this->mail = $mail;
        $this->config = $config;
        $this->config['subject'] = $this->config['subject'] ?? 'Your ticket';

        $this->delegateRepository = $delegateRepository;
    }

    public function handle(DomainMessage $domainMessage)
    {
        $message = $domainMessage->getMessage();
        if (!($message instanceof CheckinIdGenerated)) {
            return;
        }

        /** @var Delegate $delegate */
        $delegate = $this->delegateRepository->get($message->getId());

        $viewModel = new ViewModel(['delegate' => $delegate, 'config'=> $this->config, 'checkinId' => $message->getCheckinId()]);
        $viewModel->setTemplate('email/ticket');

        $response = new Response();
        $this->view->setResponse($response);
        $this->view->render($viewModel);
        $html = $response->getContent();

        $emailMessage = $this->buildMessage($html);
        $emailMessage->setTo($delegate->getContactEmail());

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
