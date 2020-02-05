<?php

namespace ConferenceTools\Attendance\Controller;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use ConferenceTools\Attendance\Domain\DataSharing\Command\AddDelegate;
use ConferenceTools\Attendance\Domain\DataSharing\OptInConsent;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\DelegateList;
use ConferenceTools\Attendance\Domain\DataSharing\ReadModel\Delegate as DelegateListDelegate;
use ConferenceTools\Attendance\Domain\Delegate\Command\ResendTicketEmail;
use ConferenceTools\Attendance\Domain\Delegate\Command\UpdateDelegateDetails;
use ConferenceTools\Attendance\Domain\Delegate\DietaryRequirements;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Sponsor\ReadModel\Sponsor;
use ConferenceTools\Attendance\Form\DelegateForm;
use Doctrine\Common\Collections\Criteria;
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

    public function viewOptInsAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');
        $delegate = $this->repository(Delegate::class)->get($delegateId);
        /** @var DelegateList[] $delegateLists */
        $delegateLists = $this->indexBy(
            $this->repository(DelegateListDelegate::class)
            ->matching(Criteria::create()->where(Criteria::expr()->eq('delegateId', $delegateId)))
            ->map(function (DelegateListDelegate $delegate) { return $delegate->getDelegateList();}
            )
        );

        $sponsors = $this->repository(Sponsor::class)->matching(Criteria::create());

        return new ViewModel(['delegate' => $delegate, 'lists' => $delegateLists, 'sponsors' => $sponsors]);
    }

    public function changeOptInsAction()
    {
        $delegateListId = $this->params()->fromRoute('delegateListId');
        /** @var DelegateList $list */
        $list = $this->repository(DelegateList::class)->get($delegateListId);

        if ($list->isListAvailable()) {
            $this->flashMessenger()->addWarningMessage('This list has been collected by the sponsor, if you want to change your preferences you will need to get in touch with them directly');
            return $this->redirect()->toRoute('attendance/delegates/view-opt-ins', [], [], true);
        }

        $questions = $list->getOptIns();
        $delegateId = $this->params()->fromRoute('delegateId');
        $delegateListDelegate = $list->getDelegate($delegateId);
        $answers = $delegateListDelegate->getConsents();

        if ($this->getRequest()->isPost()) {
            // form data cannot be invalid, so it's not validated
            $data = $this->params()->fromPost();
            $optIns = [];
            foreach ($questions as $question) {
                $optIns[] = new OptInConsent($question->getHandle(), (bool) $data[$question->getHandle()]);
            }
            $this->messageBus()->fire(new AddDelegate($delegateListId, $delegateId, ...$optIns));
            $this->flashMessenger()->addSuccessMessage('Your preferences have been updated.');
            return $this->redirect()->toRoute('attendance/delegates/view-opt-ins', [], [], true);
        }

        return new ViewModel(['questions' => $questions, 'answers' => $answers]);
    }

    public function qrCodeAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');
        $delegate = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('checkinId', $delegateId)))->first();

        if (!($delegate instanceof Delegate)) {
            return $this->notFoundAction();
        }

        $renderer = new QRCode(new QROptions(['outputType' => QRCode::OUTPUT_IMAGE_PNG, 'imageBase64' => false]));
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

    public function badgeAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');
        $delegate = $this->repository(Delegate::class)->get($delegateId);

        if (!($delegate instanceof Delegate)) {
            return $this->notFoundAction();
        }

        $renderer = new QRCode(new QROptions(['outputType' => QRCode::OUTPUT_MARKUP_SVG, 'imageBase64' => false]));
        $qrCode = $renderer->render($delegate->getCheckinId());

        $response = $this->getResponse();
        if ($response instanceof Response) {
            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'image/svg+xml');
        }

        $viewModel = new ViewModel(['delegate' => $delegate, 'qrCode' => $qrCode]);
        $viewModel->setTerminal(true);
        return $viewModel;
    }

    public function resendTicketEmailAction()
    {
        $delegateId = $this->params()->fromRoute('delegateId');
        /** @var Delegate $delegate */
        $delegate = $this->repository(Delegate::class)->get($delegateId);

        $this->messageBus()->fire(new ResendTicketEmail($delegateId, $delegate->getEmail(), $delegate->getCheckinId()));

        $this->flashMessenger()->addInfoMessage('Email resent');
        $this->redirect()->toRoute('attendance/purchase/complete', ['purchaseId' => $delegate->getPurchaseId()]);
    }
}