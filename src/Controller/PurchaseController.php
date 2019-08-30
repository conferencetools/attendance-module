<?php

namespace ConferenceTools\Attendance\Controller;

use ConferenceTools\Attendance\Domain\Delegate\Command\RegisterDelegate;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use ConferenceTools\Attendance\Domain\Delegate\Event\DelegateRegistered;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountCode;
use ConferenceTools\Attendance\Domain\Discounting\ReadModel\DiscountType;
use ConferenceTools\Attendance\Domain\Merchandise\ReadModel\Merchandise;
use ConferenceTools\Attendance\Domain\Payment\Command\ProvidePaymentDetails;
use ConferenceTools\Attendance\Domain\Payment\Command\SelectPaymentMethod;
use ConferenceTools\Attendance\Domain\Payment\PaymentType;
use ConferenceTools\Attendance\Domain\Payment\ReadModel\Payment;
use ConferenceTools\Attendance\Domain\Purchasing\BasketFactory;
use ConferenceTools\Attendance\Domain\Purchasing\Command\AllocateTicketToDelegate;
use ConferenceTools\Attendance\Domain\Purchasing\Command\ApplyDiscount;
use ConferenceTools\Attendance\Domain\Purchasing\Command\Checkout;
use ConferenceTools\Attendance\Domain\Purchasing\Command\PurchaseItems;
use ConferenceTools\Attendance\Domain\Purchasing\Event\TicketsReserved;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use ConferenceTools\Attendance\Domain\Purchasing\TicketQuantity;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Ticket;
use ConferenceTools\Attendance\Form\DelegatesForm;
use ConferenceTools\Attendance\Form\PurchaseForm;
use ConferenceTools\Attendance\PaymentProvider\PaymentProvider;
use ConferenceTools\Attendance\Service\TicketValidationFailed;
use Doctrine\Common\Collections\Criteria;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class PurchaseController extends AppController
{
    protected $tickets;
    private $paymentProvider;

    public function __construct(PaymentProvider $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

    public function indexAction()
    {
        //$this->messageBus()->fire(new CreateWebhook('stripe-payment-provider/webhooks/payment-intent-success', 'https://946f48c4.ngrok.io'));
        $events = $this->getTicketService()->getTicketsForPurchasePage();
        $tickets = $this->getTickets(true);
        $form = $this->form(PurchaseForm::class, ['tickets' => $tickets]);

        //@TODO if discount code in url fetch + validate it.
        //@TODO if discount code in url, apply it to prices (use <strike></strike>)

        if ($this->getRequest()->isPost()) {
            $formData = $this->params()->fromPost();
            $form->setData($formData);
            if ($form->isValid()) {
                $data = $form->getData();
                if ($this->validateTicketQuantity($data['quantity']) && $this->validateDiscountCode($data['discount_code'])) {

                    $minDelegates = PHP_INT_MAX;
                    $maxDelegates = 0;

                    foreach ($data['quantity'] as $ticketId => $quantity) {
                        $quantity = (int)$quantity;
                        $minDelegates = min($minDelegates, $quantity);
                        $maxDelegates += $quantity;
                    }

                    $forDelegates = min($maxDelegates, max($minDelegates, (int) $data['delegates']));

                    $basketFactory = new BasketFactory(
                        $this->repository(Ticket::class),
                        $this->repository(Event::class),
                        $this->repository(Merchandise::class)
                    );

                    $messages = $this->messageBus()->fire(new PurchaseItems($data['purchase_email'], $forDelegates, $basketFactory->createBasket($data['quantity'], [])));
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
        return new ViewModel(['tickets' => $tickets, 'events' => $events, 'form' => $form]);
    }

    public function delegatesAction()
    {
        $tickets = $this->getTickets(false);
        $purchaseId = $this->params()->fromRoute('purchaseId');

        /** @var Purchase $purchase*/
        $purchase = $this->repository(Purchase::class)->get($purchaseId);

        foreach ($purchase->getTickets() as $ticketId => $quantity) {
            $ticketOptions[$ticketId] = $tickets[$ticketId]->getDescriptor()->getName();
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

                        if (empty($delegate['email'])) {
                            $delegate['email'] = $purchase->getEmail();
                        }

                        $dietaryRequirements = new DietaryRequirements($delegate['preference'], $delegate['allergies']);

                        $command = new RegisterDelegate(
                            $purchaseId,
                            $delegate['name'],
                            $delegate['email'],
                            $delegate['company'],
                            $dietaryRequirements,
                            $delegate['requirements'],
                            'delegate'
                        );

                        //@TODO should message bus should reuse the correlation id between subsequent command dispatches? (or add metadata requestId)
                        $messages = $this->messageBus()->fire($command);
                        $delegateId = $this->messageBus()->firstInstanceOf(DelegateRegistered::class, ...$messages)->getId();

                        foreach ($delegate['tickets'] as $ticketId) {
                            $command = new AllocateTicketToDelegate($delegateId, $purchaseId, $ticketId);
                            $this->messageBus()->fire($command);
                        }
                    }

                    $this->messageBus()->fire(new Checkout($purchaseId));
                    return $this->redirect()->toRoute('attendance/purchase/payment', ['purchaseId' => $purchaseId]);
                }
            }
        }

        return new ViewModel(['form' =>  $form, 'purchase' => $purchase, 'tickets' => $tickets, 'delegates' => $maxDelegates]);
    }

    /* logic
    if manual payment
        add message about awaiting payment confirmation, display complete  (done)
    if automatic payment
        if payment raised
            select payment method: stripe by default (done)
        if payment started
            if method post
                update to pending
            else
                display payment form (if reloaded how to get client secret?)
        if payment pending
            display message: waiting payment confirmation; reload page after a short while (done)
        if payment complete
            display/redirect to complete page (done)
    */
    public function paymentAction()
    {
        $purchaseId = $this->params()->fromRoute('purchaseId');
        /** @var Payment $payment */
        $payment = $this->repository(Payment::class)->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $purchaseId)))->current();

        if ($payment->isComplete()) {
            return $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $payment->getPurchaseId()]);
        }

        if ($payment->getPaymentMethod() === null) {
            //@TODO allow configuration of payment types, show user a form to select the one they wish to use
            $this->messageBus()->fire(new SelectPaymentMethod($payment->getId(), new PaymentType('stripe', 1800, false)));
        }

        if ($payment->getPaymentMethod()->requiresManualConfirmation()) {
            $this->flashMessenger()->addWarningMessage('Your payment is pending manual confirmation. You will receive email confirmation once this has been done.');
            return $this->completeAction();
        }

        if ($payment->isPending()) {
            return new ViewModel(['payment' => $payment]);
        }

        if ($this->getRequest()->isPost()) {
            $this->messageBus()->fire(new ProvidePaymentDetails($payment->getId()));
            return $this->redirect()->toRoute('attendance/purchase/payment', ['purchaseId' => $payment->getPurchaseId()]);
        }

        $purchase = $this->repository(Purchase::class)->get($purchaseId);

        return $this->paymentProvider->getView($purchase, $payment);
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

        $delegates = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $purchaseId)));
        $viewModel = new ViewModel(['purchase' => $purchase, 'discount' => $discount, 'tickets' => $this->getTickets(false), 'delegates' => $delegates]);
        $viewModel->setTemplate('attendance/purchase/complete');
        return $viewModel;
    }

    private function validateTicketQuantity(array $quantities): bool
    {
        $result = $this->getTicketService()->validateTicketQuantity($quantities);
        if ($result instanceof TicketValidationFailed) {
            $this->flashMessenger()->addErrorMessage($result->getReason());
            return false;
        }

        return true;
    }

    private function validateDelegateTicketAssignment(Purchase $purchase, array $data): bool
    {
        $purchasedTickets = $purchase->getTickets();
        $maxDelegates = $purchase->getMaxDelegates();
        $tickets = $this->getTickets(true);
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
                    sprintf('You have allocated too many %s tickets.', $tickets[$ticketId]->getDescriptor()->getName())
                );
                $valid = false;
            }

            if ($quantity > 0) {
                $this->flashMessenger()->addErrorMessage(
                    sprintf('You have left %s tickets unallocated.', $tickets[$ticketId]->getDescriptor()->getName())
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

    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'amIInTheRightPlace'], 10);
    }

    /**
     * @TODO Logic pulled out of individual methods, validate it and add to it to cover payment flow and prevent jumping to complete without payment
     */
    public function amIInTheRightPlace(MvcEvent $event)
    {
        $action = $event->getRouteMatch()->getParam('action');
        if ($action === 'index' || $action === 'confirm-payment') {
            return;
        }

        $purchaseId = $this->params()->fromRoute('purchaseId');

        /** @var Purchase $purchase*/
        $purchase = $this->repository(Purchase::class)->get($purchaseId);

        if ($purchase === null) {
            $this->flashMessenger()->addErrorMessage('Purchase not found, or has timed out');
            return $this->redirect()->toRoute('attendance/purchase');
        }

        if ($purchase->isPaid() && $action !== 'complete') {
            return $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $purchaseId]);
        }

        if ($purchase->isPaid() && $action === 'complete') {
            return;
        }

        if ($action === 'payment') {
            return;
        }

        $delegates = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('purchaseId', $purchaseId)));

        if (count($delegates) > 0) {
            return $this->redirect()->toRoute('attendance/purchase/payment', ['purchaseId' => $purchaseId]);
        }
    }
}