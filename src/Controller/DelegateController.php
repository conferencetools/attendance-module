<?php


namespace ConferenceTools\Attendance\Controller;


use ConferenceTools\Attendance\Domain\Delegate\Command\DelegateDetailsUpdated;
use ConferenceTools\Attendance\Domain\Delegate\Command\UpdateDelegateDetails;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Form\Fieldset\DelegateInformation;
use GeneratedHydrator\Configuration;
use GeneratedHydrator\Factory\HydratorFactory;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\View\Model\ViewModel;

class DelegateController extends AppController
{
    public function updateDetailsAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');

        //@TODO this really really needs sorting out...
        $form = new Form();
        $form->add(['type' => DelegateInformation::class, 'name' => 'delegate']);
        $form->add(new Csrf('security'));
        $form->add(new Submit('update', ['label' => 'Update']));

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