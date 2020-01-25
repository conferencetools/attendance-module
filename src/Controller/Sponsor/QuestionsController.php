<?php

namespace ConferenceTools\Attendance\Controller\Sponsor;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\DataSharing\OptIn;
use ConferenceTools\Attendance\Domain\Sponsor\Command\AddQuestion;
use ConferenceTools\Attendance\Domain\Sponsor\Command\DeleteQuestion;
use ConferenceTools\Attendance\Form\ConfirmationForm;
use ConferenceTools\Attendance\Form\Sponsor\QuestionForm;
use Zend\View\Model\ViewModel;

class QuestionsController extends AppController
{
    public function addAction()
    {
        $sponsor = $this->currentSponsor();
        $form = $this->form(QuestionForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->messageBus()->fire(new AddQuestion($sponsor->getId(), new OptIn($data['handle'], $data['question'])));
                $this->flashMessenger()->addSuccessMessage('Question added successfully');
                return $this->redirect()->toRoute('attendance-sponsor');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Add delegate question']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function deleteAction()
    {
        $sponsor = $this->currentSponsor();
        $questionHandle = $this->params()->fromRoute('handle');
        $form = $this->form(ConfirmationForm::class);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                if ($data['cancel'] === null && $data['confirm'] === '') {
                    $this->messageBus()->fire(new DeleteQuestion($sponsor->getId(), $questionHandle));
                    $this->flashMessenger()->addSuccessMessage('Question deleted');
                }

                return $this->redirect()->toRoute('attendance-sponsor');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Are you sure you want to delete this question?']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }
}