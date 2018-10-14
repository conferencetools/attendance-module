<?php


namespace ConferenceTools\Attendance\Controller;


use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseTickets;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\TicketType;
use ConferenceTools\Attendance\Form\Fieldset\DelegateInformation;
use Doctrine\Common\Collections\Criteria;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;
use ZfrStripe\Exception\CardErrorException;

class PurchaseController extends AppController
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

    private $tickets;

    public function indexAction()
    {
        $tickets = $this->getTickets();

        //@TODO if discount code in url fetch + validate it.
        //@TODO if discount code in url, apply it to prices (use <strike></strike>)

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            if ($this->validateTicketQuantity($data['quantity'])) {

                foreach ($data['quantity'] as $ticketId => $quantity) {
                    $quantity = (int) $quantity;
                    if ($quantity > 0) {
                        $selectedTickets[] = new TicketQuantity($ticketId, $quantity);
                    }
                }
                //@TODO capture email address here instead?
                //@TODO capture GDPR confirmation
                $messages = $this->messageBus()->fire(new PurchaseTickets(...$selectedTickets));

                foreach ($messages as $message) {
                    if ($message->getMessage() instanceof TicketsReserved) {
                        $purchaseId = $message->getMessage()->getId();
                    }
                }

                //@TODO handle discount code?

                return $this->redirect()->toRoute('attendance/purchase/delegate-info', ['purchaseId' => $purchaseId]);
            }
        }

        return new ViewModel(['tickets' => $tickets]);
    }

    public function delegatesAction()
    {
        $tickets = $this->getTickets();
        $purchaseId = $this->params()->fromRoute('purchaseId');

        /** @var Purchase $purchase*/
        $purchase = $this->repository(Purchase::class)->get($purchaseId);

        foreach ($purchase->getTickets() as $ticketId => $quantity) {
            $ticketOptions[$ticketId] = $tickets[$ticketId]->getTicket()->getName();
        }

        $form = new Form();
        $maxDelegates = $purchase->getMaxDelegates();

        for ($i = 0; $i < $maxDelegates; $i++) {
            $fieldsetName = 'delegate_' . $i;
            $form->add(['type' => DelegateInformation::class, 'name' => $fieldsetName]);
            $form->get($fieldsetName)->add(
                new MultiCheckbox(
                    'tickets',
                    ['value_options' => $ticketOptions, 'label' => 'Tickets']
                )
            );
        }

        //$form->add(new Csrf('security'));
        $form->add(new Submit('continue', ['label' => 'Continue']));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            $data = $form->getData();

            if ($form->isValid() && $this->validateDelegateTicketAssignment($purchase, $data)) {
                for ($i = 0; $i < $maxDelegates; $i++) {
                    $delegate = $data['delegate_' . $i];
                    $command = new RegisterDelegate(
                        $purchaseId,
                        $delegate['firstname'],
                        $delegate['lastname'],
                        $delegate['email'],
                        $delegate['company'],
                        $delegate['twitter'],
                        $delegate['requirements']
                    );

                    $messages = $this->messageBus()->fire($command);
                    //@TODO add this functionallity to the message bus
                    //@TODO message bus should reuse the correlation id between subsequent command dispatches
                    foreach ($messages as $message) {
                        if ($message->getMessage() instanceof DelegateRegistered) {
                            $delegateId = $message->getMessage()->getId();
                            break;
                        }
                    }

                    foreach ($delegate['tickets'] as $ticketId) {
                        $command = new AllocateTicketToDelegate($delegateId, $purchaseId, $ticketId);
                        $this->messageBus()->fire($command);
                    }
                }
                return $this->redirect()->toRoute('attendance/purchase/payment', ['purchaseId' => $purchaseId]);
            }
        }

        return new ViewModel(['form' =>  $form, 'purchase' => $purchase, 'tickets' => $tickets, 'delegates' => $maxDelegates]);
    }

    public function paymentAction()
    {
        $form = new Form('', ['attributes' => ['id'=>'purchase_form']]);
        $form->add(new Hidden('stripe_token'));

        if ($this->getRequest()->isPost()) {
            try {

            } catch (CardErrorException $e) {
                //@ToDO this is currently stripe specific, perhaps move into the handler and pickup a generic exception
                $this->flashMessenger()->addErrorMessage(
                    sprintf(
                        'There was an issue with taking your payment: %s Please try again.',
                        $this->getDetailedErrorMessage($e)
                    )
                );
            }
        }

        //$form->add(new Csrf('security'));
        return new ViewModel(['form' => $form]);


    }

    /**
     * @return TicketType[]
     */
    private function getTickets(): array
    {
        if ($this->tickets === null) {
            $tickets = $this->repository(TicketType::class)->matching(new Criteria());
            $ticketsIndexed = [];

            foreach ($tickets as $ticket) {
                $ticketsIndexed[$ticket->getId()] = $ticket;
            }

            $this->tickets = $ticketsIndexed;
        }

        return $this->tickets;
    }

    private function validateTicketQuantity(array $quantities): bool
    {
        $tickets = $this->getTickets();

        $total = 0;
        foreach ($quantities as $ticketId => $quantity) {
            if ((int) $quantity > 0) { //filter out any rows which haven't been selected
                $total += $quantity;

                if (!isset($tickets[$ticketId]) || $tickets[$ticketId]->getRemaining() < $quantity) {
                    $this->flashmessenger()->addErrorMessage('One or more of the tickets you selected has sold out or you have selected more than the quantity remaining');
                    return false;
                }
            }
        }

        if ($total < 1) {
            $this->flashmessenger()->addErrorMessage('Please select at least one ticket to purchase');
            return false;
        }
    }

    private function validateDelegateTicketAssignment(Purchase $purchase, array $data): bool
    {
        $purchasedTickets = $purchase->getTickets();
        $maxDelegates = $purchase->getMaxDelegates();
        $tickets = $this->getTickets();
        $valid = true;

        for ($i = 0; $i < $maxDelegates; $i++) {
            $delegateTickets = $data['delegate_' . $i]['tickets'];
            foreach ($delegateTickets as $ticketId) {
                $purchasedTickets[$ticketId]--;
            }
        }

        foreach ($purchasedTickets as $ticketId => $quantity) {
            if ($quantity < 0) {
                $this->flashMessenger()->addErrorMessage(
                    sprintf('You have allocated too many %s tickets.', $tickets[$ticketId]->getTicket()->getName())
                );
                $valid = false;
            }

            if ($quantity > 0) {
                $this->flashMessenger()->addErrorMessage(
                    sprintf('You have left %s tickets unallocated.', $tickets[$ticketId]->getTicket()->getName())
                );
                $valid = false;
            }
        }

        return $valid;
    }

    private function getDetailedErrorMessage(CardErrorException $e)
    {
        $response = $e->getResponse();
        $errors = json_decode($response->getBody(true), true);
        $code = isset($errors['error']['code']) ? $errors['error']['code'] : 'processing_error';
        $code = isset(static::$cardErrorMessages[$code]) ? $code : 'processing_error';

        return static::$cardErrorMessages[$code];
    }
}