<?php


namespace ConferenceTools\Attendance\Controller;


use ConferenceTools\Attendance\Domain\Delegate\Command\UpdateDelegateDetails;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Form\DelegateForm;
use GeneratedHydrator\Configuration;
use Zend\View\Model\ViewModel;

class DelegateController extends AppController
{
    public function updateDetailsAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');

        $form = $this->form(DelegateForm::class);

        $delegate = $this->repository(Delegate::class)->get($delegateId);
        if ($this->getRequest()->isPost()) {
            $form->setData($this->params()->fromPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $command = new UpdateDelegateDetails(
                    $delegateId,
                    $data['delegate']['firstname'],
                    $data['delegate']['lastname'],
                    $data['delegate']['email'],
                    $data['delegate']['company'],
                    $data['delegate']['twitter'],
                    $data['delegate']['requirements']
                );
                $this->messageBus()->fire($command);
            }
        } else {
            $hydratorClass = (new Configuration(Delegate::class))->createFactory()->getHydratorClass();
            $hydrator = new $hydratorClass();
            $data = $hydrator->extract($delegate);
            $form->setData(['delegate' => $data]);
        }

        return new ViewModel(['form' => $form]);
    }
}