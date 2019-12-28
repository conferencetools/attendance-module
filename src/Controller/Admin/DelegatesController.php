<?php


namespace ConferenceTools\Attendance\Controller\Admin;


use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\Command\ResendTicketEmail;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;

class DelegatesController extends AppController
{
    public function resendTicketEmailAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');
        /** @var Delegate $delegate */
        $delegate = $this->repository(Delegate::class)->get($delegateId);

        $this->messageBus()->fire(new ResendTicketEmail($delegateId, $delegate->getEmail(), $delegate->getCheckinId()));

        $this->flashMessenger()->addInfoMessage('Email resent');
        $this->redirect()->toRoute('attendance-admin/purchase/view', ['purchaseId' => $delegate->getPurchaseId()]);
    }
}