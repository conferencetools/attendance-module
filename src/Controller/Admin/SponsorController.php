<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Sponsor\Command\CreateSponsor;
use ConferenceTools\Attendance\Domain\Sponsor\ReadModel\Sponsor;
use ConferenceTools\Attendance\Form\Admin\SponsorForm;
use ConferenceTools\Authentication\Domain\User\Command\ChangeUserPermissions;
use ConferenceTools\Authentication\Domain\User\Command\CreateNewUser;
use ConferenceTools\Authentication\Domain\User\HashedPassword;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class SponsorController extends AppController
{
    public function indexAction()
    {
        $sponsors = $this->repository(Sponsor::class)->matching(Criteria::create());
        return new ViewModel(['sponsors' => $sponsors]);
    }

    public function createAction()
    {
        $form = $this->form(SponsorForm::class);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $this->messageBus()->fire(new CreateNewUser($data['email'], new HashedPassword($data['password'])));
                $this->messageBus()->fire(new ChangeUserPermissions($data['email'], ['sponsor']));
                $this->messageBus()->fire(new CreateSponsor($data['name'], $data['email']));

                $this->flashMessenger()->addSuccessMessage('Sponsor Created');
                return $this->redirect()->toRoute('attendance-admin/sponsors');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Create new sponsor']);
        $viewModel->setTemplate('attendance/admin/form');

        return $viewModel;
    }
}