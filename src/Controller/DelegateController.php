<?php

namespace ConferenceTools\Attendance\Controller;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use ConferenceTools\Attendance\Domain\Delegate\Command\UpdateDelegateDetails;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Form\DelegateForm;
use GeneratedHydrator\Configuration;
use Zend\Http\Response;
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
                $delegateData = $data['delegate'];
                $dietaryRequirements = new DietaryRequirements($delegateData['preference'], $delegateData['allergies']);
                $command = new UpdateDelegateDetails(
                    $delegateId,
                    $delegateData['name'],
                    $delegateData['email'],
                    $delegateData['company'],
                    $dietaryRequirements,
                    $delegateData['requirements']
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

    public function qrCodeAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');
        $renderer = new QRCode(new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => false]));
        $delegate = $this->repository(Delegate::class)->get($delegateId);

        if (!($delegate instanceof Delegate)) {
            return $this->notFoundAction();
        }

        $png = $renderer->render($delegateId);

        $response = $this->getResponse();
        if ($response instanceof Response) {
            $response->setContent($png);
            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'image/png');
            $headers->addHeaderLine('Accept-Ranges', 'bytes');
            $headers->addHeaderLine('Content-Length', strlen($png));
        }

        return $response;
    }
}