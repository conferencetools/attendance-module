<?php

namespace ConferenceTools\Attendance\Controller\Admin;

use ConferenceTools\Attendance\Controller\AppController;
use ConferenceTools\Attendance\Domain\Delegate\ReadModel\Delegate;
use ConferenceTools\Attendance\Domain\Purchasing\ReadModel\Purchase;
use ConferenceTools\Attendance\Domain\Ticketing\ReadModel\Event;
use Doctrine\Common\Collections\Criteria;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

class ReportsController extends AppController
{
    public function cateringPreferencesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));
        $tickets = $this->getTickets();
        $events = $this->getEvents();
        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            foreach ($record->getTickets() as $ticketId) {
                $data[$events[$tickets[$ticketId]->getEventId()]->getDescriptor()->getName()][$record->getPreference()]++;
            }
        }

        foreach ($data as $event => $datum) {
            foreach ($datum as $preference => $count) {
                $iterator[] = [$event, ucfirst($preference), $count];
            }
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($iterator);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Catering Report', 'report' => $iterator, 'header' => ['Event', 'Food preference', 'Count']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;


    }

    public function cateringAlergiesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));

        $tickets = $this->getTickets();
        $events = $this->getEvents();
        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            if (!empty($record->getAllergies())) {
                foreach ($record->getTickets() as $ticketId) {
                    $data[] = [
                        'event' => $events[$tickets[$ticketId]->getEventId()]->getDescriptor()->getName(),
                        'name' => $record->getName(),
                        'allergies' => $record->getAllergies(),
                    ];
                }
            }
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Catering allergies Report', 'report' => $data, 'header' => ['Event', 'Delegate name', 'Alergies']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
    }

    public function delegatesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('isPaid', true)));
        $tickets = $this->getTickets();

        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            $row = [
                'name' => $record->getName(),
                'company' => $record->getCompany(),
                'type' => $record->getDelegateType(),
                'email' => $record->getContactEmail(),
            ];

            $row['tickets'] = implode('; ', \array_map(function ($ticketId) use ($tickets) {return $tickets[$ticketId]->getDescriptor()->getName();}, $record->getTickets()));
            $data[] = $row;
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Delegates Report', 'report' => $data, 'header' => ['Name', 'Company', 'Delegate type', 'Email', 'Tickets']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
    }

    public function checkedInDelegatesAction()
    {
        $records = $this->repository(Delegate::class)->matching(Criteria::create()->where(Criteria::expr()->eq('checkedIn', true)));
        $tickets = $this->getTickets();

        $data = [];

        foreach ($records as $record) {
            /** @var Delegate $record */
            $email = $record->getEmail();
            if (empty($email)) {
                $email = $record->getPurchaserEmail();
            }

            $row = [
                'name' => $record->getName(),
                'company' => $record->getCompany(),
                'type' => $record->getDelegateType(),
                'email' => $email,
            ];

            $row['tickets'] = implode('; ', \array_map(function ($ticketId) use ($tickets) {return $tickets[$ticketId]->getDescriptor()->getName();}, $record->getTickets()));
            $data[] = $row;
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Checked in Delegates Report', 'report' => $data, 'header' => ['Name', 'Company', 'Delegate type', 'Email', 'Tickets']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
    }

    public function purchasesAction()
    {
        $records = $this->repository(Purchase::class)->matching(Criteria::create());

        $data = [];

        foreach ($records as $record) {
            /** @var Purchase $record */
            $row = [
                'email' => $record->getEmail(),
                'code' => $record->getDiscountCode(),
                'delegates' => $record->getMaxDelegates(),
                'paid' => $record->isPaid() ? 'Yes' : 'No',
            ];

            $data[] = $row;
        }

        if ((bool) $this->params()->fromQuery('download', false) === true) {
            $csv = $this->createCsvData($data);
            return $this->makeResponse($csv);
        }

        $viewModel = new ViewModel(['title'=> 'Purchases Report', 'report' => $data, 'header' => ['Email', 'Discount code', 'Delegates', 'Paid']]);
        $viewModel->setTemplate('attendance/admin/reports/report');

        return $viewModel;
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

    /**
     * @param string $csv
     * @return \Zend\Stdlib\ResponseInterface
     */
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

    /** @return Event[] */
    private function getEvents()
    {
        return $this->indexBy($this->repository(Event::class)->matching(Criteria::create()));
    }
}