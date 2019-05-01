<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Ticketing\Command\CreateEvent;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use ConferenceTools\Attendance\Form\EventForm;
use Doctrine\Common\Collections\Criteria;
use Zend\View\Model\ViewModel;

class EventsController extends AppController
{
    public function indexAction()
    {
        $events = $this->repository(Event::class)->matching(Criteria::create());

        return new ViewModel(['events' => $events]);
    }

    public function newEventAction()
    {
        $form = $this->form(EventForm::class);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new CreateEvent(
                    $data['name'],
                    $data['description'],
                    $data['capacity'],
                    new \DateTime($data['startsOn']),
                    new \DateTime($data['endsOn'])
                );
                $this->messageBus()->fire($command);

                return $this->redirect()->toRoute('attendance-admin/events');
            }
        }

        $viewModel = new ViewModel(['form' => $form, 'action' => 'Create new event']);
        $viewModel->setTemplate('attendance/admin/form');
        return $viewModel;
    }
}