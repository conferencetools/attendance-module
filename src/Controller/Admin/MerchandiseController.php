<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Merchandise\Command\CreateMerchandise;
use ConferenceTools\Attendance\Domain\Merchandise\ReadModel\Merchandise;
use ConferenceTools\Attendance\Domain\Merchandise\Command\ScheduleSaleDate;
use ConferenceTools\Attendance\Domain\Merchandise\Command\ScheduleWithdrawDate;
use ConferenceTools\Attendance\Domain\Ticketing\Descriptor;
use ConferenceTools\Attendance\Domain\Ticketing\Price;
use ConferenceTools\Attendance\Form\DateTimeForm;
use ConferenceTools\Attendance\Form\MerchandiseForm;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class MerchandiseController extends AppController
{
    private $taxRate = 20;

    public function indexAction()
    {
        $merchandise = $this->repository(Merchandise::class)->matching(Criteria::create());

        return new ViewModel(['merchandise' => $merchandise]);
    }

    public function newMerchandiseAction()
    {
        $form = $this->form(MerchandiseForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new CreateMerchandise(
                    new Descriptor($data['name'], $data['description']),
                    $data['quantity'],
                    $this->makePrice($data['price'], $data['grossOrNet']),
                    (bool) $data['requiresTicket']
                );
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/merchandise');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Create new Merchandise']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function withdrawAction()
    {
        $merchandiseId = $this->params()->fromRoute('merchandiseId');
        $form = $this->form(DateTimeForm::class, ['fieldLabel' => 'Withdraw from', 'submitLabel' => 'Withdraw']);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new ScheduleWithdrawDate($merchandiseId, new \DateTime($data['datetime']));
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/merchandise');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Withdraw merchandise']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    public function putOnSaleAction()
    {
        $merchandiseId = $this->params()->fromRoute('merchandiseId');
        $form = $this->form(DateTimeForm::class, ['fieldLabel' => 'On sale from', 'submitLabel' => 'Put on Sale']);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new ScheduleSaleDate($merchandiseId, new \DateTime($data['datetime']));
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/merchandise');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Put merchandise on sale']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }

    private function makePrice($price, $grossOrNet)
    {
        if ($grossOrNet === 'gross') {
            return Price::fromGrossCost($price, $this->taxRate);
        }

        return Price::fromNetCost($price, $this->taxRate);
    }

}