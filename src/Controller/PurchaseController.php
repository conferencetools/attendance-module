<?php


namespace ConferenceTools\Attendance\Controller;


use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountCode;
use ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountType;
use ConferenceTools\Attendance\Domain\Payment\Command\TakePayment;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\ApplyDiscount;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseTickets;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Form\DelegatesForm;
use ConferenceTools\Attendance\Form\PaymentForm;
use ConferenceTools\Attendance\Form\PurchaseForm;
use ConferenceTools\Attendance\Handler\PaymentFailed;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class PurchaseController extends AppController
{
    private $tickets;

    public function indexAction()
    {
        $tickets = $this->getTickets();
        $form = $this->form(PurchaseForm::class, ['tickets' => $tickets]);

        //@TODO if discount code in url fetch + validate it.
        //@TODO if discount code in url, apply it to prices (use <strike></strike>)

        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->validateTicketQuantity($data['quantity']) && $this->validateDiscountCode($data['discount_code'])) {

                    foreach ($data['quantity'] as $ticketId => $quantity) {
                        $quantity = (int)$quantity;
                        if ($quantity > 0) {
                            $selectedTickets[] = new TicketQuantity(
                                $ticketId,
                                $tickets[$ticketId]->getEvent(),
                                $quantity,
                                $tickets[$ticketId]->getPrice()
                            );
                        }
                    }

                    //@TODO capture GDPR confirmation?
                    $messages = $this->messageBus()->fire(new PurchaseTickets($data['purchase_email'], ...$selectedTickets));
                    $purchaseId = $this->messageBus()->firstInstanceOf(TicketsReserved::class, ...$messages)->getId();

                    if (!empty($data['discount_code'])) {
                        $code = $this->fetchDiscountCode($data['discount_code']);
                        $command = new ApplyDiscount(
                            $purchaseId,
                            $code->getDiscountType()->getId(),
                            $data['discount_code'],
                            $code->getDiscountType()->getDiscount()
                        );

                        $this->messageBus()->fire($command);
                    }

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

        if ($purchase === null) {
            $this->flashMessenger()->addErrorMessage('Purchase not found, or has timed out');
            return $this->redirect()->toRoute('attendance/purchase');
        }

        if ($purchase->isPaid()) {
            return $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $purchaseId]);
        }

        $delegates = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $purchaseId)));

        if (count($delegates) > 0) {
            return $this->redirect()->toRoute('attendance/purchase/payment', ['purchaseId' => $purchaseId]);
        }

        foreach ($purchase->getTickets() as $ticketId => $quantity) {
            $ticketOptions[$ticketId] = $tickets[$ticketId]->getEvent()->getName();
        }

        $maxDelegates = $purchase->getMaxDelegates();
        $form = $this->form(DelegatesForm::class, [
            'ticketOptions' => $ticketOptions,
            'maxDelegates' => $maxDelegates
        ]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());

            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->validateDelegateTicketAssignment($purchase, $data)) {
                    for ($i = 0; $i < $maxDelegates; $i++) {
                        $delegate = $data['delegate_' . $i];

                        if (empty($delegate['tickets'])) {
                            continue;
                        }
                        $dietaryRequirements = new DietaryRequirements($delegate['preference'], $delegate['allergies']);

                        $command = new RegisterDelegate(
                            $purchaseId,
                            $delegate['name'],
                            $delegate['email'],
                            $delegate['company'],
                            $dietaryRequirements,
                            $delegate['requirements']
                        );

                        //@TODO should message bus should reuse the correlation id between subsequent command dispatches? (or add metadata requestId)
                        $messages = $this->messageBus()->fire($command);
                        $delegateId = $this->messageBus()->firstInstanceOf(DelegateRegistered::class, ...$messages)->getId();

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
        $form = $this->form(PaymentForm::class);

        $purchaseId = $this->params()->fromRoute('purchaseId');

        /** @var Purchase $purchase*/
        $purchase = $this->repository(Purchase::class)->get($purchaseId);

        $discount = null;

        if ($purchase->getDiscountId() !== null) {
            $discount = $this->repository(DiscountType::class)->get($purchase->getDiscountId());
        }

        if ($purchase === null) {
            $this->flashMessenger()->addErrorMessage('Purchase not found, or has timed out');
            return $this->redirect()->toRoute('attendance/purchase');
        }

        if ($purchase->isPaid()) {
            return $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $purchaseId]);
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                try {
                    $command = new TakePayment($purchaseId, $purchase->getTotal(), $data['stripe_token'], $purchase->getEmail());
                    $this->messageBus()->fire($command);

                    return $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $purchaseId]);
                } catch (PaymentFailed $e) {
                    $this->flashMessenger()->addErrorMessage(
                        sprintf(
                            'There was an issue with taking your payment: %s Please try again.',
                            $e->getMessage()
                        )
                    );
                }
            }
        }

        return new ViewModel(['form' => $form, 'purchase' => $purchase, 'discount' => $discount, 'tickets' => $this->getTickets()]);
    }

    public function completeAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');

        /** @var Purchase $purchase*/
        $purchase = $this->repository(Purchase::class)->get($purchaseId);

        $discount = null;

        if ($purchase->getDiscountId() !== null) {
            $discount = $this->repository(DiscountType::class)->get($purchase->getDiscountId());
        }

        if ($purchase === null) {
            $this->flashMessenger()->addErrorMessage('Purchase not found, or has timed out');
            return $this->redirect()->toRoute('attendance/purchase');
        }

        $delegates = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $purchaseId)));
        return new ViewModel(['purchase' => $purchase, 'discount' => $discount, 'tickets' => $this->getTickets(), 'delegates' => $delegates]);
    }

    /**
     * @return Ticket[]
     */
    private function getTickets(): array
    {
        if ($this->tickets === null) {
            $tickets = $this->repository(Ticket::class)->matching(Criteria::create()->where(Criteria::expr()->eq('onSale', true)));
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
                    sprintf('You have allocated too many %s tickets.', $tickets[$ticketId]->getEvent()->getName())
                );
                $valid = false;
            }

            if ($quantity > 0) {
                $this->flashMessenger()->addErrorMessage(
                    sprintf('You have left %s tickets unallocated.', $tickets[$ticketId]->getEvent()->getName())
                );
                $valid = false;
            }
        }

        return $valid;
    }

    private function validateDiscountCode(string $discountCode): bool
    {
        if (empty($discountCode)) {
            return true;
        }

        $code = $this->fetchDiscountCode($discountCode);

        $valid = ($code instanceof DiscountCode && $code->getDiscountType()->isAvailable());

        if (!$valid) {
            $this->flashMessenger()->addErrorMessage('Invalid discount code');
        }

        return $valid;
    }

    private function fetchDiscountCode(string $discountCode): ?DiscountCode
    {
        $criteria = Criteria::create()->where(Criteria::expr()->eq('code', $discountCode));
        $codes = $this->repository(DiscountCode::class)->matching($criteria);
        $code = $codes->current();
        return $code ? $code : null;
    }
}