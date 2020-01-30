<?php

namespace ConferenceTools\Attendance\Controller\Sponsor;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\DataSharing\Command\AddDelegate;
use ConferenceTools\Attendance\Domain\DataSharing\Command\CreateDelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Form\ConfirmationForm;
use ConferenceTools\Attendance\Form\Sponsor\DelegateListOptIn;
use Doctrine\Common\Collections\Criteria;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

class DelegateListController extends AppController
{
    public function createAction()
    {
        $sponsor = $this->currentSponsor();
        if ($sponsor->hasCreatedList()) {
            $this->flashMessenger()->addWarningMessage('A list has already been created');
            return $this->redirect()->toRoute('attendance-sponsor');
        }

        $form = $this->form(ConfirmationForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                if ($data['cancel'] === null && $data['confirm'] === '') {
                    $this->messageBus()->fire(new CreateDelegateList(
                        $sponsor->getId(),
                        ...array_values($sponsor->getQuestions())
                    ));

                    $this->flashMessenger()->addSuccessMessage('Your list is now ready for collecting delegates');
                }

                return $this->redirect()->toRoute('attendance-sponsor');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Are you sure you want to complete setup. Questions cannot be edited once you confirm.']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function downloadListAction()
    {

    }

    public function collectAction()
    {
        $sponsor = $this->currentSponsor();
        $sponsor->getDelegateListId();
        /** @var DelegateList $list */
        $list = $this->repository(DelegateList::class)->get($sponsor->getDelegateListId());

        if ($list->isListTerminated()) {
            $this->flashMessenger()->addWarningMessage('Delegate list collection is no longer available');
            return $this->redirect()->toRoute('attendance-sponsor');
        }

        $questions = $list->getOptIns();
        $delegate = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('checkinId', $this->params()->fromRoute('checkinId'))))->first();
        if (!($delegate instanceof Delegate)) {
            $this->flashMessenger()->addWarningMessage('Delegate not found');
            return $this->redirect()->toRoute('attendance-sponsor/delegatelist/scan');
        }

        if ($this->getRequest()->isPost()) {
            // form data cannot be invalid, so it's not validated
            $data = $this->params()->fromPost();
            $optIns = [];
            foreach ($questions as $question) {
                $optIns[] = new OptInConsent($question->getHandle(), (bool) $data[$question->getHandle()]);
            }
            $this->messageBus()->fire(new AddDelegate($sponsor->getDelegateListId(), $delegate->getId(), ...$optIns));
            $this->flashMessenger()->addSuccessMessage('Delegate added to list');
            return $this->redirect()->toRoute('attendance-sponsor/delegatelist/scan');
        }

        $viewModel = new ViewModel(['sponsor' => $sponsor, 'questions' => $questions]);
        return $viewModel;
    }

    public function scanAction()
    {
        return new ViewModel([]);
    }
}