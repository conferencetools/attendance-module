<?php

namespace ConferenceTools\Attendance\Controller\Sponsor;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\DataSharing\Command\AddDelegate;
use ConferenceTools\Attendance\Domain\DataSharing\Command\CreateDelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Form\ConfirmationForm;
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

    public function manageListAction()
    {
        // show qr code scanner
    }

    public function addDelegateAction()
    {
        $checkinId = $this->params()->fromRoute('checkinId'); // load delegate, get real id
        /** @var Delegate $delegate */
        $delegate = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('checkinId', $checkinId)))->current();
        $delegateId = $delegate->getId();
        $sponsor = $this->currentSponsor();
        $listId = $sponsor->getDelegateListId();
        $form = new Form();
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->messageBus()->fire(new AddDelegate(
                    $listId,
                    $delegateId,
                    ...$this->makeQuestionResponses($data['questions'])
                ));
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /** @return OptInConsent[] */
    private function makeQuestionResponses(array $data): array
    {
        $result = [];
        foreach ($data as $handle => $optIn) {
            $result[] = new OptInConsent($handle, $optIn);
        }

        return $result;
    }
}