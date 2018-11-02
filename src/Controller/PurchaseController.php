<?php


namespace ConferenceTools\Attendance\Controller;


use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Payment\Command\TakePayment;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseTickets;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\TicketType;
use ConferenceTools\Attendance\Form\Fieldset\DelegateInformation;
use Doctrine\Common\Collections\Criteria;
use TwbBundle\Form\View\Helper\TwbBundleForm;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\MultiCheckbox;
use Zend\Form\Element\Number;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Validator\EmailAddress;
use Zend\Validator\NotEmpty;
use Zend\View\Model\ViewModel;
use ZfrStripe\Exception\CardErrorException;

class PurchaseController extends AppController
{
    private $tickets;

    public function indexAction()
    {
        $tickets = $this->getTickets();
        $form = new Form();
        $fieldset = new Fieldset('quantity');
        foreach ($tickets as $ticketId => $ticket) {
            $fieldset->add((new Number($ticketId))->setAttributes(['class' => 'form-control','min' => 0,'max' => $ticket->getRemaining(), 'value' => 0]));
        }
        $form->add($fieldset);
        $form->add([
            'type' => Text::class,
            'name' => 'purchase_email',
            'options' => [
                'label' => 'Email',
                'label_attributes' => ['class' => 'col-sm-4 control-label'],
                'twb-layout' => TwbBundleForm::LAYOUT_HORIZONTAL,
                'column-size' => 'sm-8',
            ],
            'attributes' => ['class' => 'form-control', 'placeholder' => 'Your receipt will be emailed to this address']
        ]);
        $form->getInputFilter()
            ->get('purchase_email')
            ->setAllowEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()
            ->attach(new NotEmpty())
            ->attach(new EmailAddress());


        //@TODO if discount code in url fetch + validate it.
        //@TODO if discount code in url, apply it to prices (use <strike></strike>)

        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->validateTicketQuantity($data['quantity'])) {

                    foreach ($data['quantity'] as $ticketId => $quantity) {
                        $quantity = (int)$quantity;
                        if ($quantity > 0) {
                            $selectedTickets[] = new TicketQuantity($ticketId, $tickets[$ticketId]->getTicket(), $quantity);
                        }
                    }
                    //@TODO capture email address here instead?
                    //@TODO capture GDPR confirmation
                    $messages = $this->messageBus()->fire(new PurchaseTickets($data['purchase_email'], ...$selectedTickets));

                    foreach ($messages as $message) {
                        if ($message->getMessage() instanceof TicketsReserved) {
                            $purchaseId = $message->getMessage()->getId();
                        }
                    }

                    //@TODO handle discount code?

                    return $this->redirect()->toRoute('attendance/purchase/delegate-info', ['purchaseId' => $purchaseId]);
                }
            }
        }
        $form->prepare();
        return new ViewModel(['tickets' => $tickets, 'form' => $form]);
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

        //@TODO this really really needs sorting out...
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

        $form->add(new Csrf('security'));
        $form->add(new Submit('continue', ['label' => 'Continue']));

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->validateDelegateTicketAssignment($purchase, $data)) {
                    for ($i = 0; $i < $maxDelegates; $i++) {
                        if (empty($delegate['tickets'])) {
                            continue;
                        }
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
        }

        return new ViewModel(['form' =>  $form, 'purchase' => $purchase, 'tickets' => $tickets, 'delegates' => $maxDelegates]);
    }

    public function paymentAction()
    {
        $form = new Form('payment-form');
        $form->add(new Hidden('stripe_token'));
        $form->add(new Csrf('security'));

        $purchaseId = $this->params()->fromRoute('purchaseId');

        /** @var Purchase $purchase*/
        $purchase = $this->repository(Purchase::class)->get($purchaseId);

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            try {
                $command = new TakePayment($purchaseId, $purchase->getTotal(), $data['stripe_token'], $purchase->getEmail());
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $purchaseId]);
            } catch (\Exception $e) {
                $this->flashMessenger()->addErrorMessage(
                    sprintf(
                        'There was an issue with taking your payment: %s Please try again.',
                        $e->getMessage()
                    )
                );
            }
        }

        return new ViewModel(['form' => $form, 'purchase' => $purchase,'tickets' => $this->getTickets()]);
    }

    public function completeAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');

        /** @var Purchase $purchase*/
        $purchase = $this->repository(Purchase::class)->get($purchaseId);
        return new ViewModel(['purchase' => $purchase,'tickets' => $this->getTickets()]);
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

        return true;
    }

    private function validateDelegateTicketAssignment(Purchase $purchase, array $data): bool
    {
        $purchasedTickets = $purchase->getTickets();
        $maxDelegates = $purchase->getMaxDelegates();
        $tickets = $this->getTickets();
        $valid = true;

        for ($i = 0; $i < $maxDelegates; $i++) {
            $delegateTickets = $data['delegate_' . $i]['tickets'];
            foreach ((array) $delegateTickets as $ticketId) {
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
}