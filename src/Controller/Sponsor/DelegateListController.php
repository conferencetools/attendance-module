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
use Zend\Http\Response;
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
        $sponsor = $this->currentSponsor();
        if ($sponsor->getDelegateListId() === null) {
            $this->flashMessenger()->addErrorMessage('You haven\'t created a list yet');
            return $this->redirect()->toRoute('attendance-sponsor');
        }

        /** @var DelegateList $delegateList */
        $delegateList = $this->repository(DelegateList::class)->get($sponsor->getDelegateListId());

        if (!$delegateList->isListAvailable()) {
            $this->flashMessenger()->addWarningMessage('This list is not available for download yet, please check back at ' . $delegateList->getAvailableTime()->format('Y-m-d H:i'));
            return $this->redirect()->toRoute('attendance-sponsor');
        }

        $delegatesOnList = [];

        foreach ($delegateList->getDelegates() as $delegate) {
            if ($delegate->includeOnList()) {
                $delegatesOnList[$delegate->getDelegateId()] = $delegate;
            }
        }

        /** @var Delegate[] $delegates */
        $delegates = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->in('id', array_keys($delegatesOnList))));

        $questions = $delegateList->getOptIns();
        foreach ($questions as $question) {
            $consents[$question->getHandle()] = $question->getQuestion();
            $templateConsents[$question->getHandle()] = 'No';
        }

        $datum = [
            'Name',
            'Email',
            'Company',
        ];

        ksort($consents);
        $datum += $consents;

        $data = [$datum];

        foreach ($delegates as $delegate) {
            $datum = [
                'name' => $delegate->getName(),
                'email' => $delegate->getEmail(),
                'company' => $delegate->getCompany(),
            ];

            $consents = $templateConsents;

            foreach ($delegatesOnList[$delegate->getId()]->getConsents() as $handle => $consent) {
                $consents[$handle] = $consent ? 'Yes' : 'No';
            }

            ksort($consents);
            $datum += $consents;

            $data[] = $datum;
        }

        return $this->makeResponse($this->createCsvData($data));
    }

    /**
     * Csv output function inspired by https://github.com/opencfp/opencfp/blob/master/src/Http/Controller/Admin/ExportsController.php
     * @param iterable $data
     * @return string
     */
    private function createCsvData(iterable $data): string
    {
        \ob_start();
        $f = \fopen('php://output', 'w');

        $escape = function (string $record): string {
            return \preg_replace('#^([=+\-@]{1})#','\'\1', $record);
        };

        foreach ($data as $row) {
            \fputcsv($f, \array_map($escape, $row));
        }

        \fclose($f);

        return \ob_get_clean();
    }

    private function makeResponse(string $csv): \Zend\Stdlib\ResponseInterface
    {
        $response = $this->getResponse();
        if ($response instanceof Response) {
            $response->setContent($csv);
            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Type', 'text/csv');
            $headers->addHeaderLine('Content-Disposition', 'attachment; filename="export.csv"');
            $headers->addHeaderLine('Accept-Ranges', 'bytes');
            $headers->addHeaderLine('Content-Length', strlen($csv));
        }

        return $response;
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